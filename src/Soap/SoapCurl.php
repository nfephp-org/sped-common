<?php

/**
 * SoapClient based in cURL class
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Soap\SoapCurl
 * @copyright NFePHP Copyright (c) 2016-2019
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-common for the canonical source repository
 */

namespace NFePHP\Common\Soap;

use CurlHandle;
use NFePHP\Common\Exception\SoapException;
use NFePHP\Common\Validator;
use NFePHP\Common\Certificate;

class SoapCurl extends SoapBase implements SoapInterface
{
    /**
     * Handle cURL persistente, nivel-processo. Mantido vivo entre instancias
     * de SoapCurl (cada documento cria uma nova) para reaproveitar a conexao
     * TCP/TLS e evitar refazer o handshake (~2.3s) a cada requisicao.
     * Funciona em qualquer versao de PHP (nao depende de CURL_LOCK_DATA_CONNECT).
     * @var CurlHandle|resource|null
     */
    private static $persistentCurl = null;

    /**
     * Timestamp (epoch) do ultimo uso do handle persistente.
     * @var int
     */
    private static $lastUsedAt = 0;

    /**
     * Segundos de ociosidade apos os quais o handle e descartado e a conexao
     * refeita (o SVRS fecha conexoes ociosas; reusar um socket morto causaria
     * reset by peer). 30s.
     */
    private const IDLE_LIMIT = 30;

    /**
     * Constructor
     * @param Certificate $certificate
     */
    public function __construct(Certificate $certificate = null)
    {
        parent::__construct($certificate);
    }

    /**
     * Send soap message to url
     * @param string $url
     * @param string $operation
     * @param string $action
     * @param int $soapver
     * @param array $parameters
     * @param array $namespaces
     * @param string $request
     * @param \SoapHeader $soapheader
     * @return string
     * @throws \NFePHP\Common\Exception\SoapException
     */
    public function send(
        $url,
        $operation = '',
        $action = '',
        $soapver = SOAP_1_2,
        $parameters = [],
        $namespaces = [],
        $request = '',
        $soapheader = null
    ) {
        //check or create key files
        //before send request
        $this->saveTemporarilyKeyFiles();
        $response = '';
        $envelope = $this->makeEnvelopeSoap(
            $request,
            $namespaces,
            $soapver,
            $soapheader
        );
        $msgSize = strlen($envelope);
        $parameters = [
            "Content-Type: application/soap+xml;charset=utf-8;",
            "Content-length: $msgSize"
        ];
        if (!empty($action)) {
            $parameters[0] .= "action=$action";
        }
        $this->requestHead = implode("\n", $parameters);
        $this->requestBody = $envelope;
        try {
            // reutiliza o handle persistente do processo (mantem a conexao viva).
            // se ficou ocioso demais, descarta para reconectar (evita socket morto).
            if (self::$persistentCurl !== null
                && (time() - self::$lastUsedAt) > self::IDLE_LIMIT
            ) {
                curl_close(self::$persistentCurl);
                self::$persistentCurl = null;
            }
            if (self::$persistentCurl === null) {
                self::$persistentCurl = curl_init();
            }
            $oCurl = self::$persistentCurl;
            $this->setCurlProxy($oCurl);
            curl_setopt($oCurl, CURLOPT_URL, $url);
            curl_setopt($oCurl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $this->soaptimeout);
            curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->soaptimeout + 20);
            curl_setopt($oCurl, CURLOPT_HEADER, 1);
            curl_setopt($oCurl, CURLOPT_HTTP_VERSION, $this->httpver);
            // keep-alive ativo para manter a conexao ociosa aberta entre documentos
            curl_setopt($oCurl, CURLOPT_TCP_KEEPALIVE, 1);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
            if (!empty($this->security_level)) {
                curl_setopt($oCurl, CURLOPT_SSL_CIPHER_LIST, "{$this->security_level}");
            }
            if (!$this->disablesec) {
                curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 2);
                if (!empty($this->casefaz)) {
                    if (is_file($this->casefaz)) {
                        curl_setopt($oCurl, CURLOPT_CAINFO, $this->casefaz);
                    }
                }
            }
            curl_setopt($oCurl, CURLOPT_SSLVERSION, $this->soapprotocol);
            curl_setopt($oCurl, CURLOPT_SSLCERT, $this->tempdir . $this->certfile);
            curl_setopt($oCurl, CURLOPT_SSLKEY, $this->tempdir . $this->prifile);
            if (!empty($this->temppass)) {
                curl_setopt($oCurl, CURLOPT_KEYPASSWD, $this->temppass);
            }
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            if (!empty($envelope)) {
                curl_setopt($oCurl, CURLOPT_POST, 1);
                curl_setopt($oCurl, CURLOPT_POSTFIELDS, $envelope);
                curl_setopt($oCurl, CURLOPT_HTTPHEADER, $parameters);
            }
            $response = curl_exec($oCurl);
            self::$lastUsedAt = time();
            $this->soaperror = curl_error($oCurl);
            $this->soaperror_code = curl_errno($oCurl);
            $ainfo = curl_getinfo($oCurl);
            if (is_array($ainfo)) {
                $this->soapinfo = $ainfo;
            }
            $headsize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
            $httpcode = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
            // NAO fechar o handle: mantem a conexao TCP/TLS no pool para o proximo documento.
            // em erro de transporte, descarta o handle para forcar reconexao limpa.
            if ($this->soaperror_code !== 0) {
                curl_close($oCurl);
                self::$persistentCurl = null;
            }
            $this->responseHead = trim(substr($response, 0, $headsize));
            $this->responseBody = trim(substr($response, $headsize));
            $this->saveDebugFiles(
                $operation,
                $this->requestHead . "\n" . $this->requestBody,
                $this->responseHead . "\n" . $this->responseBody
            );
        } catch (\Exception $e) {
            throw SoapException::unableToLoadCurl($e->getMessage());
        }
        if ($this->soaperror != '') {
            if ((int)$this->soaperror_code == 0) {
                $this->soaperror_code = 7;
            }
            throw SoapException::soapFault($this->soaperror . " [$url]", $this->soaperror_code);
        }
        if ($httpcode != 200) {
            $msg = $this->getCodeMessage($httpcode);
            if ((int) $httpcode == 0) {
                $httpcode = 52;
            } elseif ((int) $httpcode == 500) {
                $httpcode = 89;
            }
            throw SoapException::soapFault($msg, $httpcode);
        }
        if (empty($this->responseBody)) {
            throw SoapException::soapFault('Retorno da SEFAZ VAZIO', 99);
        }
        if (!Validator::isXML($this->responseBody)) {
            throw SoapException::soapFault('O retorno não é um XML ' . $this->responseBody, 99);
        }
        return $this->responseBody;
    }

    /**
     * Extrai mensagem da liste de erros HTTP
     * @param integer $code
     * @return string
     */
    private function getCodeMessage($code)
    {
        $codes = json_decode(file_get_contents(__DIR__ . '/httpcodes.json'), true);
        if (!empty($codes[$code])) {
            return $codes[$code]['description'];
        }
        return "Erro desconhecido.";
    }

    /**
     * Set proxy into cURL parameters
     * @param resource|CurlHandle $oCurl
     */
    private function setCurlProxy(&$oCurl)
    {
        if ($this->proxyIP != '') {
            curl_setopt($oCurl, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($oCurl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($oCurl, CURLOPT_PROXY, $this->proxyIP . ':' . $this->proxyPort);
            if ($this->proxyUser != '') {
                curl_setopt($oCurl, CURLOPT_PROXYUSERPWD, $this->proxyUser . ':' . $this->proxyPass);
                curl_setopt($oCurl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            }
        }
    }

    /**
     * Verify if URL is active
     * @param string $url
     * @return boolean
     * @throws \NFePHP\Common\Exception\SoapException
     */
    public function checkWsdlActive($url)
    {
        if (strtoupper(substr($url, -5)) != '?wsdl') {
            $url .= "?wsdl";
        }
        $this->saveTemporarilyKeyFiles();
        try {
            $oCurl = curl_init();
            $this->setCurlProxy($oCurl);
            curl_setopt($oCurl, CURLOPT_URL, $url);
            curl_setopt($oCurl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $this->soaptimeout);
            curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->soaptimeout + 20);
            curl_setopt($oCurl, CURLOPT_HEADER, 1);
            curl_setopt($oCurl, CURLOPT_HTTP_VERSION, $this->httpver);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, $this->soapprotocol);
            curl_setopt($oCurl, CURLOPT_SSLCERT, $this->tempdir . $this->certfile);
            curl_setopt($oCurl, CURLOPT_SSLKEY, $this->tempdir . $this->prifile);
            if (!empty($this->temppass)) {
                curl_setopt($oCurl, CURLOPT_KEYPASSWD, $this->temppass);
            }
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($oCurl);
            $this->soaperror = curl_error($oCurl);
            $this->soaperror_code = curl_errno($oCurl);
            $ainfo = curl_getinfo($oCurl);
            if (is_array($ainfo)) {
                $this->soapinfo = $ainfo;
            }
            $headsize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
            $httpcode = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
            curl_close($oCurl);
            $this->responseHead = trim(substr($response, 0, $headsize));
            $this->responseBody = trim(substr($response, $headsize));
        } catch (\Exception $e) {
            throw SoapException::unableToLoadCurl($e->getMessage());
        }
        if ($this->soaperror != '') {
            if (intval($this->soaperror_code) == 0) {
                $this->soaperror_code = 7;
            }
            throw SoapException::soapFault($this->soaperror . " [$url]", $this->soaperror_code);
        }
        if ($httpcode != 200) {
            if (intval($httpcode) == 0) {
                $httpcode = 500;
            }
            throw SoapException::soapFault(" [$url]" . $this->responseHead, $httpcode);
        }
        if (!Validator::isXml($this->responseBody)) {
            throw SoapException::soapFault(" [$url]" . $this->responseHead, 500);
        }
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($this->responseBody, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        $node = $dom->getElementsByTagName('definitions')->item(0);
        if (empty($node)) {
            throw SoapException::soapFault("Erro interno do Servidor", 500);
        }
        return true;
    }
}

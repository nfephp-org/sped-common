<?php

namespace NFePHP\Common\Soap;

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

use NFePHP\Common\Soap\SoapBase;
use NFePHP\Common\Soap\SoapInterface;
use NFePHP\Common\Exception\SoapException;
use NFePHP\Common\Certificate;
use Psr\Log\LoggerInterface;

class SoapCurl extends SoapBase implements SoapInterface
{
    /**
     * Constructor
     * @param Certificate $certificate
     * @param LoggerInterface $logger
     */
    public function __construct(Certificate $certificate = null, LoggerInterface $logger = null)
    {
        parent::__construct($certificate, $logger);
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
            $oCurl = curl_init();
            $this->setCurlProxy($oCurl);
            curl_setopt($oCurl, CURLOPT_URL, $url);
            curl_setopt($oCurl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            //curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT_MS, 1);
            //curl_setopt($oCurl, CURLOPT_TIMEOUT_MS, 1);
            curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $this->soaptimeout);
            curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->soaptimeout + 20);
            curl_setopt($oCurl, CURLOPT_HEADER, 1);
            curl_setopt($oCurl, CURLOPT_HTTP_VERSION, $this->httpver);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
            if (!$this->disablesec) {
                curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 2);
                if (is_file($this->casefaz)) {
                    curl_setopt($oCurl, CURLOPT_CAINFO, $this->casefaz);
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
            $this->saveDebugFiles(
                $operation,
                $this->requestHead . "\n" . $this->requestBody,
                $this->responseHead . "\n" . $this->responseBody
            );
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
                $httpcode = 52;
            }
            throw SoapException::soapFault(" [$url]" . $this->responseHead, $httpcode);
        }
        return $this->responseBody;
    }
    
    /**
     * Set proxy into cURL parameters
     * @param resource $oCurl
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
}

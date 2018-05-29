<?php

namespace NFePHP\Common\Soap;

/**
 * SoapClient based in cURL class
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Soap\SoapCurl
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\Soap\SoapBase;
use NFePHP\Common\Soap\SoapInterface;
use NFePHP\Common\Soap\SoapData;
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
        $data = new SoapData();
        $data->urlService = $url;
        $data->urlAction = $action;
        $data->soapNamespaces = $namespaces;
        $data->envelopedData = $this->makeEnvelopeSoap($request, $namespaces, $soapver, $soapheader);
        $data->contentType = 'application/soap+xml';

        return $this->send2($data);
    }
    
    public function send2(SoapData $data): string
    {
        $parameters = self::buildParameters($data);
        $this->requestHead = implode('\n', $parameters);
        $this->requestBody = $data->envelopedData;

        $url = $data->urlService;
        
        $httpcode = 0;
        try {
            $curl = $this->create_curl($url, $data->envelopedData, $parameters);
            
            $response = curl_exec($curl);

            $httpcode = $this->dispose_curl($curl, $response, $data->urlMethod);
        } catch (\Exception $e) {
            throw SoapException::unableToLoadCurl($e->getMessage());
        }

        if ($this->soaperror != '') {
            throw SoapException::soapFault($this->soaperror . " [$url]");
        }

        if ($httpcode != 200) {
            throw SoapException::soapFault(" [$url]" . $this->responseHead);
        }

        return $this->responseBody;
    }

    private static function buildParameters(SoapData $data): array
    {
        $msgSize = strlen($data->envelopedData);
        $parameters = array(
            "Content-Type: $data->contentType;charset=\"utf-8\"",
            "Content-Length: $msgSize",
        );
        if (!empty($data->urlMethod)) {
            $parameters[0] .= ";action=\"$data->urlMethod\"";
        }
        if (!empty($data->urlAction)) {
            $parameters[] = "SOAPAction: $data->urlAction";
        }
        return $parameters;
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
    
    private function createCurl($url, $envelope, $parameters)
    {
        $oCurl = curl_init();
        $this->setCurlProxy($oCurl);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $this->soaptimeout);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->soaptimeout + 30);
        curl_setopt($oCurl, CURLOPT_HEADER, 1);
        curl_setopt($oCurl, CURLOPT_VERBOSE, 1);
        // curl_setopt($oCurl, CURLOPT_FAILONERROR, 1);
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

        return $oCurl;
    }

    private function disposeCurl($oCurl, $response, $operation = '')
    {
        $this->soaperror = curl_error($oCurl);
        $ainfo = curl_getinfo($oCurl);

        if (is_array($ainfo)) {
            $this->soapinfo = $ainfo;
        }

        $headsize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
        $httpcode = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
        curl_close($oCurl);

        if ($response) {
            $this->responseHead = trim(substr($response, 0, $headsize));
            $this->responseBody = trim(substr($response, $headsize));
        }

        $this->saveDebugFiles(
            $operation,
            $this->requestHead . "\n" . $this->requestBody,
            $this->responseHead . "\n" . $this->responseBody
        );
        
        return $httpcode;
    }
}

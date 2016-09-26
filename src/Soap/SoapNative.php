<?php

namespace NFePHP\Common\Soap;

/**
 * SoapClient based in native PHP SoapClient class
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Soap\SoapNative
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\Soap\SoapClientExtended;
use NFePHP\Common\Soap\SoapBase;
use NFePHP\Common\Soap\SoapInterface;
use NFePHP\Common\Exception\SoapException;

class SoapNative extends SoapBase implements SoapInterface
{
    /**
     * Send soap message to url
     * @param string $url
     * @param string $operation
     * @param string $action
     * @param int $soapver
     * @param array $parameters
     * @param array $namespaces
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
        $request = ''
    ) {
        $this->prepare($url, $soapver);
        try {
            $response = $this->connection->$operation($parameters);
            $this->requestHead = $this->connection->__getLastRequestHeaders();
            $this->requestBody = $this->makeEnvelopeSoap(
                $request,
                $operation,
                $namespaces,
                $soapver
            );
            $this->responseHead = $this->connection->__getLastResponseHeaders();
            $this->responseBody = $this->connection->__getLastResponse();
        } catch (SoapFault $e) {
            throw SoapException::soapFault($e->getMessage());
        } catch (Exception $e) {
            throw SoapException::soapFault($e->getMessage());
        }
        return $this->responseBody;
    }
    
    /**
     * Prepare connection
     * @param string $url
     * @param int $soapver
     * @throws RuntimeException
     * @throws \NFePHP\Common\Exception\SoapException
     */
    protected function prepare($url, $soapver = SOAP_1_2)
    {
        $wsdl = "$url?WSDL";
        $params = [
            'local_cert' => $this->certfile,
            'passphrase' => '',
            'connection_timeout' => $this->soaptimeout,
            'encoding' => 'UTF-8',
            'verifypeer' => false,
            'verifyhost' => false,
            'soap_version' => $soapver,
            'trace' => true,
            'cache_wsdl' => WSDL_CACHE_NONE
        ];
        $this->setNativeProxy($params);
        try {
            $this->connection = new SoapClientExtended($wsdl, $params);
        } catch (SoapFault $e) {
            throw SoapException::soapFault($e->getMessage());
        } catch (Exception $e) {
            throw SoapException::soapFault($e->getMessage());
        }
    }
    
    /**
     * Set parameters for proxy
     * @param array $params
     */
    private function setNativeProxy(&$params)
    {
        if ($this->proxyIP != '') {
            $pproxy1 = [
                'proxy_host' => $this->proxyIP,
                'proxy_port' => $this->proxyPort
            ];
            array_push($params, $pproxy1);
        }
        if ($this->proxyUser != '') {
            $pproxy2 = [
                'proxy_login' => $this->proxyUser,
                'proxy_password' => $this->proxyPass
            ];
            array_push($params, $pproxy2);
        }
    }
}

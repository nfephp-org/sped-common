<?php

namespace NFePHP\Common\Soap;

/**
 * Soap fake class used for development only
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Soap\SoapFake
 * @copyright NFePHP Copyright (c) 2017
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

class SoapFake extends SoapBase implements SoapInterface
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
        $envelope = $this->makeEnvelopeSoap(
            $request,
            $namespaces,
            $soapver,
            $soapheader
        );
        $msgSize = strlen($envelope);

        switch ($soapver) {
            case SOAP_1_1:
                $parameters[] = "Content-Type: text/xml;charset=UTF-8;";
                if (!empty($action)) {
                    $parameters[] = "SOAPAction: \"$action\"";
                }
                $parameters[] = "Content-length: $msgSize";
                break;
            case SOAP_1_2:
            default:
                $parameters = [
                    "Content-Type: application/soap+xml;charset=utf-8;",
                    "Content-length: $msgSize"
                ];
                if (!empty($action)) {
                    $parameters[0] .= "action=$action";
                }
                break;
        }
        $requestHead = implode("\n", $parameters);
        $requestBody = $envelope;

        return json_encode([
            'url' => $url,
            'operation' => $operation,
            'action' => $action,
            'soapver' => $soapver,
            'parameters' => $parameters,
            'header' => $requestHead,
            'namespaces' => $namespaces,
            'body' => $requestBody
        ], JSON_PRETTY_PRINT);
    }
}

<?php

namespace NFePHP\Common\Soap;

/**
 * Soap class interface
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Soap\SoapInterface
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

use NFePHP\Common\Certificate;
use Psr\Log\LoggerInterface;

interface SoapInterface
{
    
    //constants
    const SSL_DEFAULT = 0; //default
    const SSL_TLSV1 = 1; //TLSv1
    const SSL_SSLV2 = 2; //SSLv2
    const SSL_SSLV3 = 3; //SSLv3
    const SSL_TLSV1_0 = 4; //TLSv1.0
    const SSL_TLSV1_1 = 5; //TLSv1.1
    const SSL_TLSV1_2 = 6; //TLSv1.2
    
    /**
     *
     * @param Certificate $certificate
     */
    public function loadCertificate(Certificate $certificate);
    
    /**
     * Set logger class
     * @param LoggerInterface $logger
     */
    public function loadLogger(LoggerInterface $logger);
    
    /**
     * Set timeout for connection
     * @param int $timesecs
     */
    public function timeout($timesecs);
    
    /**
     * Set security protocol for soap communications
     * @param int $protocol
     */
    public function protocol($protocol = self::SSL_DEFAULT);
    
    /**
     * Set proxy parameters
     * @param string $ip
     * @param int $port
     * @param string $user
     * @param string $password
     */
    public function proxy($ip, $port, $user, $password);
    
    /**
     * Force http protocol version
     * @param null|string $version '1.0', '1.1', '2.0'
     */
    public function httpVersion($version = null);
    
    /**
     * Disables the security checking of host and peer certificates
     * @param bool $flag
     * @return bool
     */
    public function disableSecurity($flag = false);

    /**
     * ONlY for tests
     * @param bool $flag
     * @return bool
     */
    public function disableCertValidation($flag = true);
    
    /**
     * Set option to encrypt private key before save in filesystem
     * for an additional layer of protection
     * @param bool $encript
     * @return bool
     */
    public function setEncriptPrivateKey($encript = true);
    
    /**
     * Set another temporayfolder for saving certificates for SOAP utilization
     * @param string | null $folderRealPath
     * @return void
     */
    public function setTemporaryFolder($folderRealPath = null);
    
    /**
     * Set debug mode, this mode will save soap envelopes in temporary directory
     * @param bool $value
     * @return bool
     */
    public function setDebugMode($value = false);
    
    /**
     * Set prefixes
     * @param array $prefixes
     * @return string[]
     */
    public function setSoapPrefix($prefixes = []);
    
    /**
     * Send soap message
     * @param string $url
     * @param string $operation
     * @param string $action
     * @param int $soapver
     * @param array $parameters
     * @param array $namespaces
     * @param \SoapHeader $soapheader
     * @param string $request
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
    );
}

<?php

namespace NFePHP\Common\Certificate;

/**
 * Class for management and use of digital certificates A1 (PKCS # 12)
 * @category   NFePHP
 * @package    NFePHP\Common\ProvateKey
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Antonio Spinelli <tonicospinelli85 at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\Exception\CertificateException;

class PrivateKey implements SignatureInterface
{
    /**
     * @var string
     */
    private $rawKey;

    /**
     * @var resource
     */
    private $resource;
    
    /**
     * @var array
     */
    private $details = [];

    /**
     * PublicKey constructor.
     * @param string $privateKey Content of private key file
     */
    public function __construct($privateKey)
    {
        $this->rawKey = $privateKey;
        $this->read();
    }

    /**
     * Get a private key
     * @link http://php.net/manual/en/function.openssl-pkey-get-private.php
     * @return void
     * @throws CertificateException An error has occurred when get private key
     */
    protected function read()
    {
        if (!$resource = openssl_pkey_get_private($this->rawKey)) {
            throw CertificateException::getPrivateKey();
        }
        $this->details = openssl_pkey_get_details($resource);
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function sign($content, $algorithm = OPENSSL_ALGO_SHA1)
    {
        $encryptedData = '';
        if (!openssl_sign($content, $encryptedData, $this->resource, $algorithm)) {
            throw CertificateException::signContent();
        }
        return $encryptedData;
    }
    
    /**
     * Return the modulus of private key
     * @return string
     */
    public function modulus()
    {
        if (empty($this->details['rsa']['n'])) {
            return '';
        }
        return base64_encode($this->details['rsa']['n']);
    }
    
    /**
     * Return the expoent of private key
     * @return string
     */
    public function expoent()
    {
        if (empty($this->details['rsa']['e'])) {
            return '';
        }
        return base64_encode($this->details['rsa']['e']);
    }
    
    /**
     * Return raw private key
     * @return string
     */
    public function __toString()
    {
        return $this->rawKey;
    }
}

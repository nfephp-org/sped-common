<?php

namespace NFePHP\Common\Certificate;

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
     * PublicKey constructor.
     * @param string $privateKey Content of private key file
     */
    public function __construct($privateKey = '')
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

    public function __toString()
    {
        return $this->rawKey;
    }
}

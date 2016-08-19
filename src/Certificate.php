<?php

namespace NFePHP\Common;

use NFePHP\Common\Exception\CertificateException;

class Certificate
{
    /**
     * @var string
     */
    public $companyName;

    /**
     * @var string
     */
    public $privateKey;

    /**
     * @var string
     */
    public $publicKey;

    /**
     * @var \DateTime
     */
    public $validFrom;

    /**
     * @var \DateTime
     */
    public $validTo;

    public function __construct($content, $password = '')
    {
        $this->read($content, $password);
        $this->load();
    }

    private function read($content, $password)
    {
        $certs = [];
        if (!openssl_pkcs12_read($content, $certs, $password)) {
            throw CertificateException::unableToRead();
        }
        $this->privateKey = $certs['pkey'];
        $this->publicKey = $certs['cert'];
    }
    
    /**
     * Load info from certificate
     * @throws CertificateException
     */
    private function load()
    {
        if (!$resource = openssl_x509_read($this->publicKey)) {
            throw CertificateException::unableToOpen();
        }

        $detail = openssl_x509_parse($resource, false);
        $this->companyName = $detail['subject']['commonName'];
        $this->validFrom = \DateTime::createFromFormat('ymdHis\Z', $detail['validFrom']);
        $this->validTo = \DateTime::createFromFormat('ymdHis\Z', $detail['validTo']);
    }

    /**
     * Check if certificate has been expired.
     * @return bool Returns true when it is truth, otherwise false.
     */
    public function isExpired()
    {
        $now = new \DateTime('now');
        return $this->validFrom <= $now && $this->validTo >= $now;
    }

    public function sign($content, $algorithm = OPENSSL_ALGO_SHA1)
    {
        if (!$privateResource = openssl_pkey_get_private($this->privateKey)) {
            throw CertificateException::getPrivateKey();
        }

        $encryptedData = '';
        if (!openssl_sign($content, $encryptedData, $privateResource, $algorithm)) {
            throw CertificateException::signContent();
        }
        return $encryptedData;
    }
}

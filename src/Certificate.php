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
    }

    private function read($content, $password)
    {
        $certs = [];
        if (!openssl_pkcs12_read($content, $certs, $password)) {
            throw CertificateException::unableToRead();
        }
        if (!$resource = openssl_x509_read($certs['cert'])) {
            throw CertificateException::unableToOpen();
        }

        $detail = openssl_x509_parse($resource, false);
        $this->companyName = $detail['subject']['commonName'];
        $this->validFrom = \DateTime::createFromFormat('ymdHis\Z', $detail['validFrom']);
        $this->validTo = \DateTime::createFromFormat('ymdHis\Z', $detail['validTo']);
        $this->privateKey = $certs['pkey'];
        $this->publicKey = $certs['cert'];
    }

    /**
     * Check if certificate has been expired.
     * @return bool Returns true when it is truth, otherwise false.
     */
    public function isExpired()
    {
        $now = new \DateTime('now');
        return $this->validTo < $now;
    }

    /**
     * @see http://php.net/manual/pt_BR/function.openssl-sign.php for detail
     * @throws NFePHP\Common\Exception\CertificateException
     */
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

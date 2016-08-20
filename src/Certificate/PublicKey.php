<?php

namespace NFePHP\Common\Certificate;

use NFePHP\Common\Exception\CertificateException;

class PublicKey implements VerificationInterface
{
    /**
     * @var string
     */
    private $rawKey;

    /**
     * @var string
     */
    public $commonName;

    /**
     * @var \DateTime
     */
    public $validFrom;

    /**
     * @var \DateTime
     */
    public $validTo;

    /**
     * PublicKey constructor.
     * @param string $publicKey
     */
    public function __construct($publicKey)
    {
        $this->rawKey = $publicKey;
        $this->read();
    }

    /**
     * Parse an X509 certificate and define the information in object
     * @link http://php.net/manual/en/function.openssl-x509-read.php
     * @link http://php.net/manual/en/function.openssl-x509-parse.php
     * @return void
     * @throws CertificateException Unable to open certificate
     */
    protected function read()
    {
        if (!$resource = openssl_x509_read($this->rawKey)) {
            throw CertificateException::unableToOpen();
        }

        $detail = openssl_x509_parse($resource, false);
        $this->commonName = $detail['subject']['commonName'];
        $this->validFrom = \DateTime::createFromFormat('ymdHis\Z', $detail['validFrom']);
        $this->validTo = \DateTime::createFromFormat('ymdHis\Z', $detail['validTo']);
    }

    /**
     * Verify signature
     * @link http://php.net/manual/en/function.openssl-verify.php
     * @param string $data
     * @param string $signature
     * @param int $algorithm [optional] For more information see the list of Signature Algorithms.
     * @return int Returns true if the signature is correct, false if it is incorrect
     * @throws CertificateException An error has occurred when verify signature
     */
    public function verify($data, $signature, $algorithm = OPENSSL_ALGO_SHA1)
    {
        $verified = openssl_verify($data, $signature, $this->rawKey, $algorithm);

        if ($verified === static::SIGNATURE_ERROR) {
            throw CertificateException::signatureFailed();
        }

        return $verified === static::SIGNATURE_CORRECT;
    }

    /**
     * Check if is in valid date interval.
     * @return bool Returns true
     */
    public function isExpired()
    {
        return new \DateTime('now') > $this->validTo;
    }

    public function __toString()
    {
        return $this->rawKey;
    }
}

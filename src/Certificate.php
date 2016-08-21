<?php

namespace NFePHP\Common;

use NFePHP\Common\Certificate\PrivateKey;
use NFePHP\Common\Certificate\PublicKey;
use NFePHP\Common\Certificate\SignatureInterface;
use NFePHP\Common\Certificate\VerificationInterface;
use NFePHP\Common\Exception\CertificateException;

class Certificate implements SignatureInterface, VerificationInterface
{
    /**
     * @var PrivateKey
     */
    public $privateKey;

    /**
     * @var PublicKey
     */
    public $publicKey;

    public function __construct(PrivateKey $privateKey, PublicKey $publicKey)
    {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
    }

    public static function readPfx($content, $password)
    {
        $certs = [];
        if (!openssl_pkcs12_read($content, $certs, $password)) {
            throw CertificateException::unableToRead();
        }
        return new static(new PrivateKey($certs['pkey']), new PublicKey($certs['cert']));
    }

    /**
     * Gets company name.
     * @return string
     */
    public function getCompanyName()
    {
        return $this->publicKey->commonName;
    }

    /**
     * Gets start date.
     * @return \DateTime Returns start date.
     */
    public function getValidFrom()
    {
        return $this->publicKey->validFrom;
    }

    /**
     * Gets end date.
     * @return \DateTime Returns end date.
     */
    public function getValidTo()
    {
        return $this->publicKey->validTo;
    }

    /**
     * Check if certificate has been expired.
     * @return bool Returns true when it is truth, otherwise false.
     */
    public function isExpired()
    {
        return $this->publicKey->isExpired();
    }

    /**
     * {@inheritdoc}
     */
    public function sign($content, $algorithm = OPENSSL_ALGO_SHA1)
    {
        return $this->privateKey->sign($content, $algorithm);
    }

    /**
     * {@inheritdoc}
     */
    public function verify($data, $signature, $algorithm = OPENSSL_ALGO_SHA1)
    {
        return $this->publicKey->verify($data, $signature, $algorithm);
    }
}

<?php

namespace NFePHP\Common;

/**
 * Certificate class for management and use of digital certificates A1 (PKCS # 12)
 * @category   NFePHP
 * @package    NFePHP\Common\Certificate
 * @copyright  Copyright (c) 2008-2017
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Antonio Spinelli <tonicospinelli85 at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\Certificate\PrivateKey;
use NFePHP\Common\Certificate\PublicKey;
use NFePHP\Common\Certificate\CertificationChain;
use NFePHP\Common\Certificate\SignatureInterface;
use NFePHP\Common\Certificate\VerificationInterface;
use NFePHP\Common\Exception\CertificateException;

class Certificate implements SignatureInterface, VerificationInterface
{
    /**
     * @var \NFePHP\Common\Certificate\PrivateKey
     */
    public $privateKey;

    /**
     * @var \NFePHP\Common\Certificate\PublicKey
     */
    public $publicKey;
    
    /**
     * @var \NFePHP\Common\Certificate\CertificationChain
     */
    public $chainKeys;
    
    /**
     * Constructor
     * @param \NFePHP\Common\Certificate\PrivateKey $privateKey
     * @param \NFePHP\Common\Certificate\PublicKey $publicKey
     * @param \NFePHP\Common\Certificate\CertificationChain $chainKeys
     */
    public function __construct(PrivateKey $privateKey, PublicKey $publicKey, CertificationChain $chainKeys)
    {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->chainKeys = $chainKeys;
    }
    
    /**
     * Read PFX and return this class
     * @param string $content
     * @param string $password
     * @return \NFePHP\Common\Certificate
     * @throws CertificateException
     */
    public static function readPfx($content, $password)
    {
        $certs = [];
        if (!openssl_pkcs12_read($content, $certs, $password)) {
            throw CertificateException::unableToRead();
        }
        $chain = '';
        if (!empty($certs['extracerts'])) {
            foreach ($certs['extracerts'] as $ec) {
                $chain .= $ec;
            }
        }
        return new static(
            new PrivateKey($certs['pkey']),
            new PublicKey($certs['cert']),
            new CertificationChain($chain)
        );
    }
    
    /**
     * Returns a PFX string with certification chain if exists
     * @param string $password
     * @return string
     */
    public function writePfx($password)
    {
        $password = trim($password);
        if (empty($password)) {
            return '';
        }
        $x509_cert = openssl_x509_read("{$this->publicKey}");
        $privateKey_resource = openssl_pkey_get_private("{$this->privateKey}");
        $pfxstring = '';
        openssl_pkcs12_export(
            $x509_cert,
            $pfxstring,
            $privateKey_resource,
            $password,
            $this->chainKeys->getExtraCertsForPFX()
        );
        return $pfxstring;
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
     * Gets CNPJ by OID '2.16.76.1.3.3' from ASN.1 certificate struture
     * @return string
     */
    public function getCnpj()
    {
        return $this->publicKey->cnpj;
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
    
    /**
     * Returns public key and chain in PEM format
     * @return string
     */
    public function __toString()
    {
        $chainKeys = '';
        if ($this->chainKeys != null) {
            $chainKeys = "{$this->chainKeys}";
        }
        return "{$this->publicKey}{$chainKeys}";
    }
}

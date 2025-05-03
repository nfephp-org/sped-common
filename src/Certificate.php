<?php

/**
 * Certificate class for management and use of digital certificates A1 (PKCS # 12)
 * @category   NFePHP
 * @package    NFePHP\Common\Certificate
 * @copyright  Copyright (c) 2008-2017
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Antonio Spinelli <tonicospinelli85 at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

namespace NFePHP\Common;

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
        if (!pkcs12Read($content, $certs, $password)) {
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
     * Function that analyzes an array of pkcs12 certificates
     * @param string $certificate
     * @param array $certInfo
     * @param string $password
     * @return array|string[]
     */
    function pkcs12Read(string $certificate, array &$certInfo, string $password): bool
    {
        if (openssl_pkcs12_read($certificate, $certInfo, $password)) {
            return true;
        }
        $msg = openssl_error_string();
        if ($msg === 'error:0308010C:digital envelope routines::unsupported') {
            if (!shell_exec('openssl version')) {
                return false;
            }
            $tempPassword = tempnam(sys_get_temp_dir(), 'pfx');
            $tempEncriptedOriginal = tempnam(sys_get_temp_dir(), 'original');
            $tempEncriptedRepacked = tempnam(sys_get_temp_dir(), 'repacked');
            $tempDecrypted = tempnam(sys_get_temp_dir(), 'decripted');
            file_put_contents($tempPassword, $password);
            file_put_contents($tempEncriptedOriginal, $certificate);
            shell_exec(<<<REPACK_COMMAND
                cat $tempPassword | openssl pkcs12 -legacy -in $tempEncriptedOriginal -nodes -out $tempDecrypted -passin stdin &&
                cat $tempPassword | openssl pkcs12 -in $tempDecrypted -export -out $tempEncriptedRepacked -passout stdin
                REPACK_COMMAND
            );
            $certificateRepacked = file_get_contents($tempEncriptedRepacked);
            unlink($tempPassword);
            unlink($tempEncriptedOriginal);
            unlink($tempEncriptedRepacked);
            unlink($tempDecrypted);
            openssl_pkcs12_read($certificateRepacked, $certInfo, $password);
            return true;
        }
        return false;
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
        return $this->publicKey->cnpj();
    }

    /**
     * Gets CPF by OID '2.16.76.1.3.1' from ASN.1 certificate struture
     * @return string
     */
    public function getCpf()
    {
        return $this->publicKey->cpf();
    }

    /**
     * Retorna o nome do ICP (Autoridade Certificadora Raiz)
     * @return string
     */
    public function getICP()
    {
        return $this->publicKey->icp;
    }

    /**
     * Retorna a URL do para a cadeia de certificação
     * @return string
     */
    public function getCAurl()
    {
        return $this->publicKey->caurl;
    }

    /**
     * Retorna a certificadora
     * @return string
     */
    public function getCSP()
    {
        return $this->publicKey->cspName;
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

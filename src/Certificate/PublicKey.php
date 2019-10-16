<?php

namespace NFePHP\Common\Certificate;

/**
 * Management and use of digital certificates A1 (PKCS # 12).
 * @category   NFePHP
 * @package    NFePHP\Common\PublicKey
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Antonio Spinelli <tonicospinelli85 at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

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
     * @var string
     */
    public $emailAddress;
    /**
     * @var string Cryptographic Service Provider
     */
    public $cspName;
    /**
     * @var string
     */
    public $serialNumber;
    /**
     * @var string
     */
    public $subjectNameValue;

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
     * Load class with certificate content
     * @param string $content
     * @return \NFePHP\Common\Certificate\PublicKey
     */
    public static function createFromContent($content)
    {
        $content = rtrim(chunk_split(preg_replace('/[\r\n]/', '', $content), 64, PHP_EOL));
        $certificate = <<<CONTENT
-----BEGIN CERTIFICATE-----
{$content}
-----END CERTIFICATE-----

CONTENT;

        return new static($certificate);
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
        if (isset($detail['subject']['emailAddress'])) {
            $this->emailAddress = $detail['subject']['emailAddress'];
        }
        if (isset($detail['issuer']['organizationalUnitName'])) {
            $this->cspName = is_array($detail['issuer']['organizationalUnitName'])
                ? implode(', ', $detail['issuer']['organizationalUnitName'])
                : $detail['issuer']['organizationalUnitName'];
        }
        $this->serialNumber = $detail['serialNumber'];
        $this->validFrom = \DateTime::createFromFormat('ymdHis\Z', $detail['validFrom']);
        $this->validTo = \DateTime::createFromFormat('ymdHis\Z', $detail['validTo']);
        if (isset($detail['name'])) {
            $arrayName = explode("/", $detail["name"]);
            $arrayName = array_reverse($arrayName);
            $arrayName = array_filter($arrayName);
            $name = implode(",", $arrayName);
            $this->subjectNameValue = $name;
        }
    }

    /**
     * Verify signature
     * @link http://php.net/manual/en/function.openssl-verify.php
     * @param string $data
     * @param string $signature
     * @param int $algorithm [optional] For more information see the list of Signature Algorithms.
     * @return bool Returns true if the signature is correct, false if it is incorrect
     * @throws CertificateException An error has occurred when verify signature
     */
    public function verify($data, $signature, $algorithm = OPENSSL_ALGO_SHA1)
    {
        $verified = openssl_verify($data, $signature, $this->rawKey, $algorithm);
        if ($verified === self::SIGNATURE_ERROR) {
            throw CertificateException::signatureFailed();
        }
        return $verified === self::SIGNATURE_CORRECT;
    }

    /**
     * Check if is in valid date interval.
     * @return bool Returns true
     */
    public function isExpired()
    {
        return new \DateTime('now') > $this->validTo;
    }

    /**
     * Returns raw public key without markers and LF's
     * @return string
     */
    public function unFormated()
    {
        $ret = preg_replace('/-----.*[\n]?/', '', $this->rawKey);
        return preg_replace('/[\n\r]/', '', $ret);
    }
    
    /**
     * Returns raw public key
     * @return string
     */
    public function __toString()
    {
        return $this->rawKey;
    }
    
    /**
     * Extract CNPJ number by OID
     * @return string
     */
    public function cnpj()
    {
        return Asn1::getCNPJ($this->unFormated());
    }

    /**
     * Extract CPF number by OID
     * @return string
     */
    public function cpf()
    {
        return Asn1::getCPF($this->unFormated());
    }
}

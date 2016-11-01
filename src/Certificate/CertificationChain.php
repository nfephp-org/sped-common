<?php

namespace NFePHP\Common\Certificate;

use NFePHP\Common\Certificate\PublicKey;

class CertificationChain
{
    /**
     * @var string
     */
    private $rawKey = '';
    /**
     * @var array
     */
    private $chainKeys = [];
    
    /**
     * Certification Chain Keys constructor
     * @param string $chainkeysstring
     */
    public function __construct($chainkeysstring = '')
    {
        $this->rawKey = $chainkeysstring;
        $this->loadListChain();
    }
    
    /**
     * Add new certificate to certification chain
     * @param string $cert Certificate in CER or PEM format
     * @return array
     */
    public function add($certificate)
    {
        return $this->loadList($certificate);
    }
    
    /**
     * Remove certificate from certification chain by there common name
     */
    public function removeExiredCertificates()
    {
        foreach ($this->chainKeys as $key => $publickey) {
            if ($publickey->isExpired()) {
                unset($this->chainKeys[$key]);
            }
        }
    }
    
    /**
     * List certificates from actual certification chain
     * @return string
     */
    public function listChain()
    {
        return $this->chainKeys;
    }
    
    /**
     * Retuns all certificates in chain as string
     * @return string
     */
    public function __toString()
    {
        $this->rawString();
        return $this->rawKey;
    }
    
    /**
     *
     */
    private function loadListChain()
    {
        $arr = explode("-----END CERTIFICATE-----", $this->rawKey);
        foreach ($arr as $a) {
            if (strlen($a) > 20) {
                $cert = "$a-----END CERTIFICATE-----\n";
                $this->loadList($cert);
            }
        }
    }
    
    private function loadList($certificate)
    {
        $publickey = new PublicKey($certificate);
        $this->chainKeys[$publickey->commonName] = $publickey;
        return $this->chainKeys;
    }
    
    private function rawString()
    {
        $this->rawKey = '';
        foreach ($this->chainKeys as $publickey) {
            $this->rawKey .= "{$publickey}";
        }
    }
}

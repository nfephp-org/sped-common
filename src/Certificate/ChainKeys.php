<?php

namespace NFePHP\Common\Certificate;

use NFePHP\Common\Certificate\PublicKey;

class ChainKeys
{
    /**
     * @var string
     */
    private $rawKey;
    /**
     * @var array
     */
    public $chainKeys = [];
    
    /**
     * Certification Chain Keys constructor
     * @param string $certstring
     */
    public function __construct($chainstring = '')
    {
        $this->rawKey = $chainstring;
        $this->loadListChain();
    }
    
    /**
     * Add new certificate to certification chain
     * @param string $cert Certificate in CER or PEM format
     * @return string
     */
    public function add($cert)
    {
        $commonName = $this->loadList($cert);
        return $commonName;
    }
    
    /**
     * Remove certificate from certification chain by there common name
     * @param string $key
     */
    public function remove($key = '')
    {
        if (key_exists($key, $this->chainKeys)) {
            unset($this->chainKeys[$key]);
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
    
    public function __toString()
    {
        $this->rawString();
        return $this->rawKey;
    }
    
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
    
    private function loadList($cert)
    {
        $pk = new PublicKey($cert);
        $this->chainKeys[$pk->commonName] = [
            'commonName' => $pk->commonName,
            'validTo' => $pk->validTo->format('Y-m-d'),
            'content' => $cert
        ];
        return $pk->commonName;
    }
    
    private function rawString()
    {
        $this->rawKey = '';
        foreach ($this->chainKeys as $cert) {
            $this->rawKey .= $cert['content'];
        }
        $this->rawKey = str_replace(
            "-----END CERTIFICATE-----\n",
            "-----END CERTIFICATE-----",
            $this->rawKey
        );
    }
}

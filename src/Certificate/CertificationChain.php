<?php

namespace NFePHP\Common\Certificate;

/**
 * Class for management and inclusion of certification chains to the public keys
 * of digital certificates model A1 (PKCS # 12)
 * @category   NFePHP
 * @package    NFePHP\Common\CertificationChain
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

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

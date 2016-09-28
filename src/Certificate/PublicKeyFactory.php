<?php

namespace NFePHP\Common\Certificate;

use NFePHP\Common\Certificate\PublicKey;

class PublicKeyFactory extends PublicKey
{
    public function __construct($unformatedkey)
    {
        $certificate =  "-----BEGIN CERTIFICATE-----\n"
            . $this->splitLines($unformatedkey)
            . "\n-----END CERTIFICATE-----\n";
        parent::__construct($certificate);
    }
    
    /**
     * splitLines
     * Split a string into lines with 76 characters (original standatd)
     * @param string $unformatedkey
     * @return string
     */
    private function splitLines($unformatedkey)
    {
        return rtrim(chunk_split(str_replace(["\r", "\n"], '', $unformatedkey), 76, "\n"));
    }
}

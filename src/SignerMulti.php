<?php

/**
 * Class to sign multiple times the same xml
 */

namespace NFePHP\Common;

class SignerMulti extends Signer
{
    /**
     * @param string $content
     * @return false
     */
    public static function existsSignature($content)
    {
        return false;
    }
}

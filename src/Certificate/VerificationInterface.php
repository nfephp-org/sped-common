<?php

namespace NFePHP\Common\Certificate;

/**
 * Interface for signature verification with digital certificates A1 (PKCS#12)
 * @category   NFePHP
 * @package    NFePHP\Common\VerificationInterface
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Antonio Spinelli <tonicospinelli85 at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\Exception\CertificateException;

interface VerificationInterface
{
    const SIGNATURE_CORRECT = 1;

    const SIGNATURE_INCORRECT = 0;

    const SIGNATURE_ERROR = -1;

    /**
     * Verify signature
     * @link http://php.net/manual/en/function.openssl-verify.php
     * @param string $data
     * @param string $signature
     * @param int $algorithm [optional] For more information see the list of Signature Algorithms.
     * @return bool Returns true if the signature is correct, false if it is incorrect
     * @throws CertificateException An error has occurred when verify signature
     */
    public function verify($data, $signature, $algorithm = OPENSSL_ALGO_SHA1);
}

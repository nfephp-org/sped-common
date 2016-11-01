<?php

namespace NFePHP\Common\Certificate;

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
     * @return int Returns true if the signature is correct, false if it is incorrect
     * @throws CertificateException An error has occurred when verify signature
     */
    public function verify($data, $signature, $algorithm = OPENSSL_ALGO_SHA1);
}

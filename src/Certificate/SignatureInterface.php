<?php

namespace NFePHP\Common\Certificate;

use NFePHP\Common\Exception\CertificateException;

interface SignatureInterface
{
    /**
     * Generate signature.
     * @link http://php.net/manual/en/function.openssl-sign.php
     * @param string $content
     * @param int $algorithm
     * @return string Returns the signature data.
     * @throws CertificateException
     */
    public function sign($content, $algorithm = OPENSSL_ALGO_SHA1);
}

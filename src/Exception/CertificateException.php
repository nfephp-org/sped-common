<?php

namespace NFePHP\Common\Exception;

/**
 * @category   NFePHP
 * @package    NFePHP\Common\Exception
 * @copyright  Copyright (c) 2008-2014
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/nfephp for the canonical source repository
 */
class CertificateException extends \RuntimeException implements ExceptionInterface
{
    public static function unableToRead()
    {
        return new static('Unable to read certificate, get follow error: ' . static::getOpenSSLError());
    }

    public static function unableToOpen()
    {
        return new static('Unable to open certificate, get follow error: ' . static::getOpenSSLError());
    }

    public static function signContent()
    {
        return new static(
            'An unexpected error has occurred when sign a content, get follow error: ' . static::getOpenSSLError()
        );
    }

    public static function getPrivateKey()
    {
        return new static('An error has occurred when get private key, get follow error: ' . static::getOpenSSLError());
    }

    private static function getOpenSSLError()
    {
        $error = '';
        while ($msg = openssl_error_string()) {
            $error .= "($msg)";
        }
        return $error;
    }
}

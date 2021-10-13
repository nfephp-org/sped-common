<?php

namespace NFePHP\Common\Exception;

/**
 * @category   NFePHP
 * @package    NFePHP\Common\Exception
 * @copyright  Copyright (c) 2008-2014
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */
class CertificateException extends \RuntimeException implements ExceptionInterface
{
    public static function unableToRead()
    {
        return new static('Impossivel ler o certificado, ' . static::getOpenSSLError());
    }

    public static function unableToOpen()
    {
        return new static('Impossivel abrir o certificado, ' . static::getOpenSSLError());
    }

    public static function signContent()
    {
        return new static(
            'Ocorreu um erro inesperado durante o processo de assinatura, ' . static::getOpenSSLError()
        );
    }

    public static function getPrivateKey()
    {
        return new static('Ocorreu um erro ao recuperar a chave privada, ' . static::getOpenSSLError());
    }

    public static function signatureFailed()
    {
        return new static(
            'Ocorreu um erro enquento verificava a assinatura, ' . static::getOpenSSLError()
        );
    }

    protected static function getOpenSSLError()
    {
        $error = 'ocorreu o seguinte erro: ';
        while ($msg = openssl_error_string()) {
            $error .= "($msg)";
        }
        return $error;
    }
}

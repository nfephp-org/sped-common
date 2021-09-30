<?php

namespace NFePHP\Common\Exception;

/**
 * @category   NFePHP
 * @package    NFePHP\Common\Exception
 * @copyright  Copyright (c) 2008-2017
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

class SoapException extends \RuntimeException implements ExceptionInterface
{
    public static function unableToLoadCurl($message)
    {
        return new static("Impossivel carregar cURL, "
            . "verifique se libcurl foi intalada. $message");
    }

    public static function soapFault($message, $code)
    {
        return new static("Erro de comunicação "
            . "via soap,  $message", $code);
    }
}

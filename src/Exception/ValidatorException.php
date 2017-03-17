<?php

namespace NFePHP\Common\Exception;

/**
 * @category   NFePHP
 * @package    NFePHP\Common\Exception
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

class ValidatorException extends \RuntimeException implements ExceptionInterface
{
    public static function xmlErrors(array $errors)
    {
        $msg = '';
        foreach ($errors as $error) {
            $msg .= $error->message."\n";
        }
        return new static('This XML is not valid. '.$msg);
    }
    
    public static function isNotXml()
    {
        return new static('This string is not a valid XML');
    }
}

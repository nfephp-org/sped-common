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

class SoapException extends \RuntimeException implements ExceptionInterface
{
    public static function unableToLoadCurl()
    {
        return new static('Unable to load cURL, verify libcurl is installed.');
    }

    public static function soapFault($message)
    {
        return new static('An error occurred while trying to communication via soap, ' . $message);
    }
}

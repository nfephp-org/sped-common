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

class SignerException extends \RuntimeException implements ExceptionInterface
{
    public static function digestComparisonFailed()
    {
        return new static('The XML content does not match the Digest Value.');
    }
    
    public static function tagNotFound($tag)
    {
        return new static("The specified tag <$tag> was not found in xml.");
    }
}

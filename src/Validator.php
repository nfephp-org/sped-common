<?php

namespace NFePHP\Common;

/**
 * @category   NFePHP
 * @package    NFePHP\Common\Validator
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\Exception\ValidatorException;
use DOMDocument;

class Validator
{
    /**
     * Find erros in XML validate with schema XSD
     * @param string $xml XML content
     * @param string $xsd real path to scheme file
     * @return boolean
     * @throws NFePHP\Common\Exception\ValidatorException
     */
    public static function isValid($xml, $xsd)
    {
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        libxml_clear_errors();
        $errors = [];
        if (! $dom->schemaValidate($xsd)) {
            throw ValidatorException::xmlErrors(libxml_get_errors());
        }
        return true;
    }
}

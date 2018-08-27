<?php

namespace NFePHP\Common;

/**
 * Validation class of XML structures with respect to its established
 * and defined structure in an XSD file
 * @category   NFePHP
 * @package    NFePHP\Common\Validator
 * @copyright  Copyright (c) 2008-2017
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
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
     * @throws ValidatorException
     */
    public static function isValid($xml, $xsd)
    {
        if (!self::isXML($xml)) {
            throw ValidatorException::isNotXml();
        }
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        libxml_clear_errors();
        if (! $dom->schemaValidate($xsd)) {
            $errors = [];
            foreach (libxml_get_errors() as $error) {
                $errors[] = $error->message;
            }
            throw ValidatorException::xmlErrors($errors);
        }
        return true;
    }
    
    /**
     * Check if string is a XML
     * @param string $content
     * @return boolean
     */
    public static function isXML($content)
    {
        $content = trim($content);
        if (empty($content)) {
            return false;
        }
        if (stripos($content, '<!DOCTYPE html>') !== false
           || stripos($content, '</html>') !== false
        ) {
            return false;
        }
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        simplexml_load_string($content);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        return empty($errors);
    }
}

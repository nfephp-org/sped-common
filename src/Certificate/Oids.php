<?php

namespace NFePHP\Common\Certificate;

/**
 * Class to obtain the list of OID Object Identifier of encrypted data
 * contained in a digital certificate.
 * See Oid from Abstract Syntax Notation One (ASN.1) for
 * Distinguished Encoding Rules (DER)
 * @category   NFePHP
 * @package    NFePHP\Common\Certificate\Oids
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/nfephp for the canonical source repository
 */

class Oids
{
    /**
     * @var array
     */
    private static $oidsTable = array();
            
    /**
     * Return Oid name
     * @param string $key formated OID numeric key
     * @return string
     */
    public static function getOid($key)
    {
        self::loadOids();
        if (array_key_exists($key, self::$oidsTable)) {
            return self::$oidsTable[$key];
        }
        return '';
    }
    
    /**
     * Returns all oids in the list
     * @return array
     */
    public static function listOids()
    {
        self::loadOids();
        return self::$oidsTable;
    }
    
    /**
     * Load list of oids
     */
    private static function loadOids()
    {
        $json = file_get_contents(__DIR__ .'/oids.json');
        self::$oidsTable = (array) json_decode($json, true);
    }
}

<?php

namespace NFePHP\Common\Certificate;

class Oids
{
    private static $oidsTable = array();
            
    /**
     * Return Oid name
     * @param type $key formated OID numeric key
     * @return mixed
     */
    public static function getOid($key)
    {
        self::loadOids();
        if (array_key_exists($key, self::$oidsTable)) {
            return self::$oidsTable[$key];
        }
        return false;
    }
    
    public static function listOids()
    {
        self::loadOids();
        return self::$oidsTable;
    }
    
    private static function loadOids()
    {
        $json = file_get_contents(__DIR__ .'/oids.json');
        self::$oidsTable = (array) json_decode($json, true);
    }
}

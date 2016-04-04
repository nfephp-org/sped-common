<?php

namespace NFePHP\Common\Certificate;

class Oids
{
    private static $oidsTable = array();
    
    /**
     * Construtor carrga a tabela de Oids do arquivo json
     */
    public function __construct()
    {
        $json = file_get_contents('oids.json');
        self::$oidsTable = json_decode($json, true);
    }
            
    /**
     * getOid
     * @param type $key
     * @return mixed
     */
    public static function getOid($key)
    {
        if (isset(self::$oidsTable[$key])) {
            return self::$oidsTable[$key];
        }
        return false;
    }
}

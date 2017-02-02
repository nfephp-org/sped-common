<?php

namespace NFePHP\Common;

use \InvalidArgumentException;

class UFList
{
    protected static $uflist = [
        12=>'AC',
        27=>'AL',
        13=>'AM',
        16=>'AP',
        29=>'BA',
        23=>'CE',
        53=>'DF',
        32=>'ES',
        52=>'GO',
        21=>'MA',
        31=>'MG',
        50=>'MS',
        51=>'MT',
        15=>'PA',
        25=>'PB',
        26=>'PE',
        22=>'PI',
        41=>'PR',
        33=>'RJ',
        24=>'RN',
        11=>'RO',
        14=>'RR',
        43=>'RS',
        42=>'SC',
        28=>'SE',
        35=>'SP',
        17=>'TO'
    ];
    
    /**
     * Returns abbreviation of state from your IBGE code
     * @param int $code
     * @return string
     * @throws InvalidArgumentException
     */
    public static function getUFByCode($code)
    {
        if (!key_exists($code, self::$uflist)) {
            throw new \InvalidArgumentException("cUF incorreto!");
        }
        return self::$uflist[$code];
    }
    
    /**
     * Returns IBGE code from abbreviation of state
     * @param string $uf
     * @return int
     * @throws InvalidArgumentException
     */
    public static function getCodeByUF($uf)
    {
        $uf = strtoupper($uf);
        $codelist = array_flip(self::$uflist);
        if (!key_exists($uf, $codelist)) {
            throw new \InvalidArgumentException("UF incorreto!");
        }
        return $codelist[$uf];
    }
}

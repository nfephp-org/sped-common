<?php

namespace NFePHP\Common;

/**
 * Returns IBGE code or State abbreviation
 * @category   NFePHP
 * @package    NFePHP\Common
 * @copyright  Copyright (c) 2008-2017
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\Exception\InvalidArgumentException;

class UFList
{
    protected static $uflist = [
        12=>'AC',
        27=>'AL',
        13=>'AM',
        91=>'AN',
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
        17=>'TO',
        92=>'SVCAN',
        93=>'SVCRS'
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
            throw new InvalidArgumentException(
                "cUF incorreto! [$code] não existe."
            );
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
            throw new InvalidArgumentException(
                "UF incorreto! [$uf] não existe."
            );
        }
        return $codelist[$uf];
    }
    
    /**
     * Returns UF list with UF as keys
     * @return array
     */
    public static function getListByUF()
    {
        return array_flip(self::$uflist);
    }
    
    /**
     * Returns UF list with Code as keys
     * @return array
     */
    public static function getListByCode()
    {
        return self::$uflist;
    }
}

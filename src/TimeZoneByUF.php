<?php

namespace NFePHP\Common;

/**
 * Returns Time Zone Strings for use of DateTime classes
 * @category   NFePHP
 * @package    NFePHP\Common
 * @copyright  Copyright (c) 2008-2017
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @license    https://opensource.org/licenses/MIT MIT
 * @license    http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\UFList;

class TimeZoneByUF
{
    protected static $tzd = [
        'AC'=>'America/Rio_Branco',
        'AL'=>'America/Maceio',
        'AM'=>'America/Manaus',
        'AP'=>'America/Belem',
        'BA'=>'America/Bahia',
        'CE'=>'America/Fortaleza',
        'DF'=>'America/Sao_Paulo',
        'ES'=>'America/Sao_Paulo',
        'GO'=>'America/Sao_Paulo',
        'MA'=>'America/Fortaleza',
        'MG'=>'America/Sao_Paulo',
        'MS'=>'America/Campo_Grande',
        'MT'=>'America/Cuiaba',
        'PA'=>'America/Belem',
        'PB'=>'America/Fortaleza',
        'PE'=>'America/Recife',
        'PI'=>'America/Fortaleza',
        'PR'=>'America/Sao_Paulo',
        'RJ'=>'America/Sao_Paulo',
        'RN'=>'America/Fortaleza',
        'RO'=>'America/Porto_Velho',
        'RR'=>'America/Boa_Vista',
        'RS'=>'America/Sao_Paulo',
        'SC'=>'America/Sao_Paulo',
        'SE'=>'America/Maceio',
        'SP'=>'America/Sao_Paulo',
        'TO'=>'America/Araguaina'
    ];
   
    /**
     * Return timezone string
     * @param string $uf
     * @return string
     */
    public static function get($uf)
    {
        $uf = strtoupper($uf);
        if (is_numeric($uf)) {
            $uf = UFList::getUFByCode((int) $uf);
        }
        //only for validation, if $uf dont exists throws exception
        UFList::getCodeByUF($uf);
        return self::$tzd[$uf];
    }
}

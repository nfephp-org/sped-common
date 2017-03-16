<?php

namespace NFePHP\Common;

/**
 * Class to create and validate the identification keys of electronic documents
 * from SPED
 * @category   NFePHP
 * @package    NFePHP\Common\Keys
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/nfephp for the canonical source repository
 */

class Keys
{
    /**
     * Build 44 digits keys to NFe, NFCe, CTe and MDFe
     * @param string $cUF UF number
     * @param string $ano year
     * @param string $mes month
     * @param string $cnpj
     * @param string $mod model of document 55, 65, 57 etc
     * @param string $serie
     * @param string $numero document number
     * @param string $tpEmis emission type
     * @param string $codigo random number or document number
     * @return string
     */
    public static function build(
        $cUF,
        $ano,
        $mes,
        $cnpj,
        $mod,
        $serie,
        $numero,
        $tpEmis,
        $codigo = ''
    ) {
        if ($codigo == '') {
            $codigo = $numero;
        }
        $format = "%02d%02d%02d%s%02d%03d%09d%01d%08d";
        $key = sprintf(
            $format,
            $cUF,
            $ano,
            $mes,
            $cnpj,
            $mod,
            $serie,
            $numero,
            $tpEmis,
            $codigo
        );
        return $key . self::verifyingDigit($key);
    }
    
    /**
     * Verifies that the key provided is valid
     * @param string $key
     * @return boolean
     */
    public static function isValid($key)
    {
        if (strlen($key) != 44) {
            return false;
        }
        $cDV = substr($key, -1);
        $calcDV = self::verifyingDigit(substr($key, 0, 43));
        if ($cDV === $calcDV) {
            return true;
        }
        return false;
    }
    
    /**
     * This method calculates verifying digit
     * @param string $key
     * @return string
     */
    public static function verifyingDigit($key)
    {
        if (strlen($key) != 43) {
            return '';
        }
        $multipliers = [2, 3, 4, 5, 6, 7, 8, 9];
        $iCount = 42;
        $weightedSum = 0;
        while ($iCount >= 0) {
            for ($mCount = 0; $mCount < 8 && $iCount >= 0; $mCount++) {
                $weightedSum += (substr($key, $iCount, 1) * $multipliers[$mCount]);
                $iCount--;
            }
        }
        $vdigit = 11 - ($weightedSum % 11);
        if ($vdigit > 9) {
            $vdigit = 0;
        }
        return (string) $vdigit;
    }
}

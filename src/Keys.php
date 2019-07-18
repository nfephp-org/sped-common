<?php

namespace NFePHP\Common;

/**
 * Class to create and validate the identification keys of electronic documents
 * from SPED
 * NT 2018.001 Emitente com CPF e IE
 * @category   NFePHP
 * @package    NFePHP\Common\Keys
 * @copyright  Copyright (c) 2008-2018
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux dot rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

class Keys
{
    /**
     * Build 44 digits keys to NFe, NFCe, CTe and MDFe
     * @param string $cUF UF number
     * @param string $ano year
     * @param string $mes month
     * @param string $cnpj or CPF
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
        $codigo = null
    ) {
        if (empty($codigo)) {
            $codigo = self::random();
        }
        //if a cpf with 11 digits is passed then complete with leading zeros
        //up to the 14 digits
        if (strlen($cnpj) < 14) {
            $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
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
    
    /**
     * Generate and return a 8 digits random number
     * for cNF tag
     * @param string|null $nnf
     * @return string
     */
    public static function random($nnf = null)
    {
        $loop = true;
        while ($loop) {
            $cnf = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $loop = !self::cNFIsValid($cnf);
            if (!empty($nnf)) {
                if (intval($cnf) === intval($nnf)) {
                    $loop = true;
                }
            }
        }
        return $cnf;
    }
    
    /**
     * Verify if cNF number is valid NT2019.001
     * @param string $cnf
     */
    public static function cNFIsValid($cnf)
    {
        $defs = [
            '00000000', '11111111', '22222222', '33333333', '44444444',
            '55555555', '66666666', '77777777', '88888888', '99999999',
            '12345678', '23456789', '34567890', '45678901', '56789012',
            '67890123', '78901234', '89012345', '90123456', '01234567'
        ];
        return !in_array($cnf, $defs);
    }
}

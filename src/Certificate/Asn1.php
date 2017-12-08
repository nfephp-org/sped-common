<?php

namespace NFePHP\Common\Certificate;

/**
 * Obtain the encrypted data contained in a digital certificate PKCS #12.
 * See Abstract Syntax Notation One (ASN.1)
 * for Distinguished Encoding Rules (DER)
 * This data may be formatted and encoded into multiple data formats, so to
 * extracted it is necessary to identify which format was inserted then
 * it can be decrypted in a readable structure
 * @category   NFePHP
 * @package    NFePHP\Common\Asn1
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/nfephp for the canonical source repository
 */

class Asn1
{
    /**
     * Get CNPJ owner number from digital certificate
     * (more specifically, from public key)
     * @param string $publickeyUnformated
     * @return string CNPJ
     */
    public static function getCNPJ($publickeyUnformated)
    {
        return self::getOIDdata('2.16.76.1.3.3', $publickeyUnformated);
    }

    /**
     * Recovers information regarding the OID contained in the certificate
     * method assumes that the OID is embedded within a structure of
     * type "sequence", as the first element of the structure
     * @param string $publickeyUnformated
     * @param string $oidNumber OID formated number
     * @return string
     */
    public static function getOIDdata($oidNumber, $publickeyUnformated)
    {
        $ret = '';
        $certder = base64_decode($publickeyUnformated);
        //converts the OID number from text to hexadecimal
        $oidMarker = self::oidHexMarker($oidNumber);
        //Divide the certificate using the OID as a marker,
        //geting the first part before the OID marker and
        //the other containing the OID data and so own.
        //Usually the certificate will be divided into only two parts,
        //because there are usually only one OID of each type in
        //the certificate, but can be more. In this case only
        //first occurency will be returned.
        $partes = explode($oidMarker, $certder);
        //if count($partes) > 1 so OID was located
        $tot = count($partes);
        if ($tot > 1) {
            //The beginning of the sequence that interests us, can be 3 or 2
            //digits before the start of OID, it depends on the number of
            //bytes used to identify the size of this data sequence,and before
            //len digits exists a separator digit like 0x30
            $xcv4 = substr($partes[0], -4);
            $xcv = substr($xcv4, -2);
            //exists Hex 030
            if ($xcv4[0] == chr(0x30)) {
                $xcv = $xcv4;
            } elseif ($xcv4[1] == chr(0x30)) {
                $xcv = substr($xcv4, -3);
            }
            //rebuild the sequency
            $data = $xcv . $oidMarker . $partes[1];
            //converts do decimal the second digit of sequency
            $bytes = strlen($oidMarker);
            //get length of OID data
            $len = self::getLength($data);
            //get only a string with bytes belongs to OID
            $oidData = substr($data, 2 + $bytes, $len-($bytes));
            //parse OID data many possibel formats and structures
            $head = strlen($oidData) - strlen($xcv) - 2;
            $ret = substr($oidData, -$head);
        }
        return $ret;
    }
    
    /**
     * Get length of data field of a sequency from certifcate
     * @param string $data
     * @return integer
     */
    protected static function getLength($data)
    {
        $len = ord($data[1]);
        //check if len <= 127 bytes,
        //if so, then $lenis length of content
        if ($len > 127) {
            $bytes = $len & 0x0f;
            $len = 0;
            for ($i = 0; $i < $bytes; $i++) {
                $len = ($len << 8) | ord($data[$i + 2]);
            }
        }
        return $len;
    }
    
    /**
     * Convert number OID in ASC Hex representation includes
     * in DER format certificate
     * @param string $oid OID formated number
     * @return string hexadecimal representation
     */
    protected static function oidHexMarker($oid)
    {
        $abBinary = array();
        $partes = explode('.', $oid);
        $bun = 0;
        $npart = count($partes);
        for ($num = 0; $num < $npart; $num++) {
            if ($num == 0) {
                $bun = 40 * $partes[$num];
            } elseif ($num == 1) {
                $bun +=  $partes[$num];
                $abBinary[] = $bun;
            } else {
                $abBinary = self::xBase128($abBinary, (integer) $partes[$num], true);
            }
        }
        $value = chr(0x06) . chr(count($abBinary));
        foreach ($abBinary as $item) {
            $value .= chr($item);
        }
        return $value;
    }

    /**
     * Converts to Base128
     * @param array $abIn
     * @param integer $qIn
     * @param boolean $flag
     * @return array
     */
    protected static function xBase128($abIn, $qIn, $flag)
    {
        $abc = $abIn;
        if ($qIn > 127) {
            $abc = self::xBase128($abc, floor($qIn/128), false);
        }
        $qIn2 = $qIn % 128;
        if ($flag) {
            $abc[] = $qIn2;
        } else {
            $abc[] = 0x80 | $qIn2;
        }
        return $abc;
    }
}

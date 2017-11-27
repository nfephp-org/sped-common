<?php

namespace NFePHP\Common;

/**
 * Classe auxiliar para o tratamento de strings
 * @category   NFePHP
 * @package    NFePHP\Common\Strings
 * @copyright  Copyright (c) 2008-2017
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux dot rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/nfephp for the canonical source repository
 */

class Strings
{
    /**
     * Replace all specials characters from string and retuns only 128 basics
     * NOTE: only for UTF-8
     * @param string $string
     * @return  string
     */
    public static function replaceSpecialsChars($string)
    {
        $string = trim($string);
        $aFind = ['&','á','à','ã','â','é','ê','í','ó','ô','õ','ú','ü',
            'ç','Á','À','Ã','Â','É','Ê','Í','Ó','Ô','Õ','Ú','Ü','Ç'];
        $aSubs = ['e','a','a','a','a','e','e','i','o','o','o','u','u',
            'c','A','A','A','A','E','E','I','O','O','O','U','U','C'];
        $newstr = str_replace($aFind, $aSubs, $string);
        $newstr = preg_replace("/[^a-zA-Z0-9 @,-_.;:\/]/", "", $newstr);
        return $newstr;
    }
    
    /**
     * Remove all non numeric characters from string
     * @param string $string
     * @return string
     */
    public static function onlyNumbers($string)
    {
        return preg_replace("/[^0-9]/", "", $string);
    }
    
    /**
     * Remove unwanted attributes, prefixes, sulfixes and other control
     * characters like \r \n \s \t
     * @param string $string
     * @param boolean $removeEncodingTag remove encoding tag from a xml
     * @return string
     */
    public static function clearXmlString($string, $removeEncodingTag = false)
    {
        $aFind = array(
            'xmlns:default="http://www.w3.org/2000/09/xmldsig#"',
            ' standalone="no"',
            'default:',
            ':default',
            "\n",
            "\r",
            "\t"
        );
        $retXml = str_replace($aFind, "", $string);
        $retXml = preg_replace('/(\>)\s*(\<)/m', '$1$2', $retXml);
        if ($removeEncodingTag) {
            $retXml = self::deleteAllBetween($retXml, '<?xml', '?>');
        }
        return $retXml;
    }
    
    /**
     * Remove all characters between markers
     * @param string $string
     * @param string $beginning
     * @param string $end
     * @return string
     */
    public static function deleteAllBetween($string, $beginning, $end)
    {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }
        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
        return str_replace($textToDelete, '', $string);
    }
    
    /**
     * Clears the xml after adding the protocol, removing repeated namespaces
     * @param string $string
     * @return string
     */
    public static function clearProtocoledXML($string)
    {
        $procXML = self::clearXmlString($string);
        $aApp = array('nfe','cte','mdfe');
        foreach ($aApp as $app) {
            $procXML = str_replace(
                'xmlns="http://www.portalfiscal.inf.br/'.$app.'" xmlns="http://www.w3.org/2000/09/xmldsig#"',
                'xmlns="http://www.portalfiscal.inf.br/'.$app.'"',
                $procXML
            );
        }
        return $procXML;
    }
    
    /**
     * Remove some alien chars from txt
     * @param string $txt
     * @return string
     */
    public static function removeSomeAlienCharsfromTxt($txt)
    {
        //remove CRs and TABs
        $txt = str_replace(["\r","\t"], "", $txt);
        //remove multiple spaces
        $txt = preg_replace('/(?:\s\s+)/', ' ', $txt);
        //remove spaces at begin and end of fields
        $txt = str_replace(["| "," |"], "|", $txt);
        return $txt;
    }
    
    /**
     * Creates a string ramdomically with the specified length
     * @param int $length
     * @return string
     */
    public static function randomString($length)
    {
        $keyspace = '0123456789abcdefghijklmnopqr'
            . 'stuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[rand(0, $max)];
        }
        return $str;
    }
}

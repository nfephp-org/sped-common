<?php

namespace NFePHP\Common\Strings;

/**
 * Classe auxiliar para o tratamento de strings
 * @category   NFePHP
 * @package    NFePHP\Common\Strings
 * @copyright  Copyright (c) 2008-2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux dot rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/nfephp for the canonical source repository
 */

class Strings
{
    /**
     * cleanString
     * Remove todos dos caracteres especiais do texto e os acentos
     * @param string $texto
     * @return  string Texto sem caractere especiais
     */
    public static function cleanString($texto = '')
    {
        $texto = trim($texto);
        $aFind = array('&','á','à','ã','â','é','ê','í','ó','ô','õ','ú','ü',
            'ç','Á','À','Ã','Â','É','Ê','Í','Ó','Ô','Õ','Ú','Ü','Ç');
        $aSubs = array('e','a','a','a','a','e','e','i','o','o','o','u','u',
            'c','A','A','A','A','E','E','I','O','O','O','U','U','C');
        $novoTexto = str_replace($aFind, $aSubs, $texto);
        $novoTexto = preg_replace("/[^a-zA-Z0-9 @,-.;:\/]/", "", $novoTexto);
        return $novoTexto;
    }
    
    /**
     * clearXml
     * Remove \r \n \s \t
     * @param string $xml
     * @param boolean $remEnc remover encoding do xml
     * @return string
     */
    public static function clearXml($xml, $remEnc = false)
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
        $retXml = str_replace($aFind, "", $xml);
        if ($remEnc) {
            $retXml = self::deleteAllBetween('<?xml' ,'?>', $retXml);
        }
        return $retXml;
    }
    
    /**
     * Remove all characters between markers
     * @param string $beginning
     * @param string $end
     * @param string $string
     * @return string
     */
    public static function deleteAllBetween($beginning, $end, $string) {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }
        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
        return str_replace($textToDelete, '', $string);
    }
    
    /**
     * clearProt
     * Limpa o xml após adição do protocolo
     * @param string $procXML
     * @return string
     */
    public static function clearProt($procXML = '')
    {
        $procXML = self::clearMsg($procXML);
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
     * clearMsg
     * @param string $msg
     * @return string
     */
    public static function clearMsg($msg)
    {
        $nmsg = str_replace(array(' standalone="no"','default:',':default',"\n","\r","\t"), '', $msg);
        $nnmsg = str_replace('> ', '>', $nmsg);
        if (strpos($nnmsg, '> ')) {
            $nnmsg = self::clearMsg((string) $nnmsg);
        }
        return $nnmsg;
    }
    
    public static function randomString($length)
    {
        $keyspace = '0123456789abcdefghijklmnopqr'
            . 'stuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }
}

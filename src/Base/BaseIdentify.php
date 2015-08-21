<?php

namespace Sped\Common\Base;

/**
 * Classe auxiliar para a identificação dos documentos eletrônicos
 * @category   NFePHP
 * @package    Sped\Common\Base
 * @copyright  Copyright (c) 2008-2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use Sped\Common\Dom\Dom;
use Sped\Common\Files\FilesFolders;

class BaseIdentify
{
    /**
     * Lista com a identificação das TAGs principais que identificam o documento
     * e o respectivo arquivo xsd
     * @var array 
     */
    protected static $schemesId = array();
    
    /**
     * setListSchemesId
     * @param array $aList
     */
    public static function setListSchemesId($aList = array())
    {
        if (count($aList) > 0) {
            self::$schemesId = $aList;
        }
    }
    
    /**
     * identificacao
     * Identifica o documento 
     * @param string $xml
     * @return string
     */
    public static function identificacao($xml = '', &$aResp = array())
    {
        if ($xml == '') {
            return '';
        }
        if (is_file($xml)) {
            $xml = FilesFolders::readFile($xml);
        }
        $dom = new Dom('1.0', 'utf-8');
        $dom->loadXMLString($xml);
        $key = '';
        $schId = (string) self::zSearchNode($dom, $key);
        $chave = '';
        $tpAmb = '';
        $dhEmi = '';
        if ($schId == 'nfe' || $schId == 'cte' || $schId == 'mdfe') {
            switch ($schId) {
                case 'nfe':
                    $tag = 'infNFe';
                    break;
                case 'cte':
                    $tag = 'infCTe';
                    break;
                case 'mdfe':
                    $tag = 'infMDFe';
                    break;
            }
            $chave = $dom->getChave($tag);
            $tpAmb = $dom->getNodeValue('tpAmb');
            $dhEmi = $dom->getNodeValue('dhEmi');
        }
        $aResp['Id'] =  $schId;
        $aResp['tag'] =  $key;
        $aResp['dom'] = $dom;
        $aResp['chave'] = $chave;
        $aResp['tpAmb'] = $tpAmb;
        $aResp['dhEmi'] = $dhEmi;
        return $schId;
    }
    
    /**
     * zSearchNode
     * @param Sped\Common\Dom\Dom $dom
     * @param string $dom
     * @return string
     */
    protected static function zSearchNode($dom, &$key)
    {
        foreach (self::$schemesId as $key => $schId) {
            $node = $dom->getElementsByTagName($key)->item(0);
            if (! empty($node)) {
                return $schId;
            }
        }
        return '';
    }
}

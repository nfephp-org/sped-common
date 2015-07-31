<?php

namespace SpedTest\Common;

/**
 * Class IdentifyTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use Sped\Common\Base\BaseIdentify;

if (!defined('TEST_ROOT')) {
    define('TEST_ROOT', dirname(dirname(__FILE__)));
}

class IdentifyTest extends \PHPUnit_Framework_TestCase
{
    public function testSetListSchemesId()
    {
        $this->assertNotFalse(true);
    }
    
    public function testIdentificacao()
    {
        $aList = array(
            'consReciNFe' => 'consReciNFe',
            'consSitNFe' => 'consSitNFe',
            'consStatServ' => 'consStatServ',
            'distDFeInt' => 'distDFeInt',
            'enviNFe' => 'enviNFe',
            'inutNFe' => 'inutNFe',
            'NFe' => 'nfe',
            'procInutNFe' => 'procInutNFe',
            'procNFe' => 'procNFe',
            'resEvento' => 'resEvento',
            'resNFe' => 'resNFe',
            'retConsReciNFe' => 'retConsReciNFe',
            'retConsSitNFe' => 'retConsSitNFe',
            'retConsStatServ' => 'retConsStatServ',
            'retDistDFeInt' => 'retDistDFeInt',
            'retEnviNFe' => 'retEnviNFe',
            'retInutNFe' => 'retInutNFe'
        );
        $aResp = array();
        BaseIdentify::setListSchemesId($aList);
        $xml = TEST_ROOT .
            '/fixtures/xml/NFe/35150158716523000119550010000000071000000076-protNFe.xml';
        $schem = BaseIdentify::identificacao($xml, $aResp);
        $this->assertEquals($schem, 'nfe');
    }
}

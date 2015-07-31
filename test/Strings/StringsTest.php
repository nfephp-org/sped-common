<?php

namespace SpedTest\Common;

/**
 * Class StringsTest
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

if (!defined('TEST_ROOT')) {
    define('TEST_ROOT', dirname(dirname(__FILE__)));
}

use Sped\Common\Strings\Strings;

class StringsTest extends \PHPUnit_Framework_TestCase
{
    public function testCleanString()
    {
        $txtSujo = "Esse é um código cheio de @$#$! , - . ; : / COISAS e 12093876486";
        $txtLimpo = "Esse e um codigo cheio de @ , - . ; : / COISAS e 12093876486";
        $resp = Strings::cleanString($txtSujo);
        $this->assertEquals($txtLimpo, $resp);
    }
    
    public function testClearXml()
    {
        $xmlSujo = file_get_contents(TEST_ROOT . '/fixtures/xml/NFe/xml-sujo.xml');
        $xmlLimpo1 = file_get_contents(TEST_ROOT . '/fixtures/xml/NFe/xml-limpo1.xml');
        $xmlLimpo2 = file_get_contents(TEST_ROOT . '/fixtures/xml/NFe/xml-limpo2.xml');
        
        $resp1 = Strings::clearXml($xmlSujo, false);
        $resp2 = Strings::clearXml($xmlSujo, true);
        $this->assertEquals($xmlLimpo1, $resp1);
        $this->assertEquals($xmlLimpo2, $resp2);
    }
    
    public function testClearProt()
    {
        $xmlSujo = '';
        $xmlLimpo = '';
        $resp1 = Strings::clearProt($xmlSujo);
        $this->assertEquals($xmlLimpo, $resp1);
    }
    
    public function testClearMsg()
    {
        $txtSujo = "AKJKJ >    < \n JKJS \t lkdlkd \r default:";
        $txtLimpo = "AKJKJ ><  JKJS  lkdlkd  ";
        $txt = Strings::clearMsg($txtSujo);
        $this->assertEquals($txt, $txtLimpo);
    }
}

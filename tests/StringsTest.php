<?php

use NFePHP\Common\Strings;

class StringsTest extends \PHPUnit\Framework\TestCase
{
    const TEST_XML_PATH = '/fixtures/xml/';
    
    public function testReplaceSpecialsChars()
    {
        $txtSujo = "Esse é um código cheio de @$#$! , - . ; : / COISAS e 12093876486";
        $txtLimpo = "Esse e um codigo cheio de @$#$ , - . ; : / COISAS e 12093876486";
        $resp = Strings::replaceSpecialsChars($txtSujo);
        $this->assertEquals($txtLimpo, $resp);
    }
    
    public function testReplaceUnacceptableCharacters()
    {
        $txtSujo = "Contribuições R$   200,00  @ # * IPI: 15% Caixa D'agua Rico   & Rich < > \"   \t \r \n ";
        $txtSujo .= mb_convert_encoding(" teste ç Á ã é ø", 'ISO-8859-1');
        $txtLimpo = "Contribuições R$ 200,00 @ # * IPI: 15% Caixa D&#39;agua Rico &amp; Rich &lt; &gt; &quot; teste ç Á ã é ø";
        $resp = Strings::replaceUnacceptableCharacters($txtSujo);
        $this->assertEquals($txtLimpo, $resp);
    }
    
    public function testClearXmlString()
    {
        $xmlSujo = file_get_contents(__DIR__. self::TEST_XML_PATH . 'NFe/xml-sujo.xml');
        $xmlLimpo1 = file_get_contents(__DIR__. self::TEST_XML_PATH . 'NFe/xml-limpo1.xml');
        $xmlLimpo2 = file_get_contents(__DIR__. self::TEST_XML_PATH . 'NFe/xml-limpo2.xml');
        $txtSujo = "AKJKJ >    < \n JKJS \t lkdlkd \r default:";
        $txtLimpo = "AKJKJ ><  JKJS  lkdlkd  ";
        $resp1 = Strings::clearXmlString($xmlSujo, false);
        $resp2 = Strings::clearXmlString($xmlSujo, true);
        $resp3 = Strings::clearXmlString($txtSujo);
        $this->assertEquals($xmlLimpo1, $resp1);
        $this->assertEquals($xmlLimpo2, $resp2);
        $this->assertEquals($txtLimpo, $resp3);
    }
    
    public function testClearProtocoledXML()
    {
        $xmlSujo = '';
        $xmlLimpo = '';
        $resp1 = Strings::clearProtocoledXML($xmlSujo);
        $this->assertEquals($xmlLimpo, $resp1);
    }
    
    public function testOnlyNumbers()
    {
        $expected = '123657788';
        $actual = Strings::onlyNumbers('123-65af77./88 Ç $#');
        $this->assertEquals($expected, $actual);
    }
    
    public function testRandomString()
    {
        $str = Strings::randomString(10);
        $len = strlen($str);
        $this->assertEquals($len, 10);
    }
    
    public function testDeleteAllBetween()
    {
        $str = "<?xml version=\"1.0\" encoding=\"utf-8\"?>"
                . "<soap:Envelope><soap:Body></soap:Body></soap:Envelope>";
        $beginning = '<?xml';
        $end = '?>';
        $actual = Strings::deleteAllBetween($str, $beginning, $end);
        $expected = "<soap:Envelope><soap:Body></soap:Body></soap:Envelope>";
        $this->assertEquals($expected, $actual);
    }
    
    public function testRemoveSomeAlienCharsfromTxt()
    {
        $txt = "C|PLASTFOAM                   IND. E       COM DE PLASTICOS LTDA|PLASTFOAM| 336546371113||184394 |2222600|3 |\n";
        $txt .= "V|\r\n";
        $txt .= "ZV|\t\n";
        $actual = Strings::removeSomeAlienCharsfromTxt($txt);
        $expected = "C|PLASTFOAM IND. E COM DE PLASTICOS LTDA|PLASTFOAM|336546371113||184394|2222600|3|\n";
        $expected .= "V|\n";
        $expected .= "ZV|\n";
        $this->assertEquals($expected, $actual);
    }

    public function testReplaceLineBreak()
    {
        $txt = "Texto com quebra de linha \n e mais uma quebra de linha \r e outra quebra de linha \r\n, nossa, como tem quebras de linhas nesse texto r n teste";
        $txtOk = "Texto com quebra de linha ; e mais uma quebra de linha ; e outra quebra de linha ;, nossa, como tem quebras de linhas nesse texto r n teste";
        $resp = Strings::replaceLineBreak($txt);
        $this->assertEquals($txtOk, $resp);
    }
}

<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\Validator;

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    const TEST_XML_PATH = '/fixtures/xml/';
    const TEST_XSD_PATH = '/fixtures/xsd/';
    
    public function testIsValidTrue()
    {
        $xml = file_get_contents(__DIR__ . self::TEST_XML_PATH . 'NFe/2017signed.xml');
        $xsd = __DIR__ . self::TEST_XSD_PATH . 'nfe_v3.10.xsd';
        $actual = Validator::isValid($xml, $xsd);
        $this->assertTrue($actual);
    }
    
    /**
     * @expectedException NFePHP\Common\Exception\ValidatorException
     */
    public function testIsValidFalse()
    {
        $xml = file_get_contents(__DIR__ . self::TEST_XML_PATH . 'NFe/35101158716523000119550010000000011003000000-nfeSigned.xml');
        $xsd = __DIR__ . self::TEST_XSD_PATH . 'nfe_v3.10.xsd';
        $actual = Validator::isValid($xml, $xsd);
        $this->assertFalse($actual);
    }
}

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
     * @expectedException RuntimeException
     */
    public function testIsValidWithErrors()
    {
        $this->expectException(\RuntimeException::class);

        $xml = file_get_contents(__DIR__ . self::TEST_XML_PATH . 'NFe/' .
            '35101158716523000119550010000000011003000000-nfeSigned.xml');
        $xsd = __DIR__ . self::TEST_XSD_PATH . 'nfe_v3.10.xsd';
        $actual = Validator::isValid($xml, $xsd);
        $this->assertFalse($actual);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testIsValidWithNoXML()
    {
        $this->expectException(\RuntimeException::class);

        $xml = 'alkjdkjhdshkjshsjhskjshksjh';
        $xsd = __DIR__ . self::TEST_XSD_PATH . 'nfe_v3.10.xsd';
        $actual = Validator::isValid($xml, $xsd);
        $this->assertFalse($actual);
    }

    public function testIsXML()
    {
        $resp = Validator::isXML('<!DOCTYPE html><html><body></body></html>');
        $this->assertFalse($resp);

        $resp = Validator::isXML('<?xml version="1.0" standalone="yes"?><root></root>');
        $this->assertTrue($resp);

        $resp = Validator::isXML(null);
        $this->assertFalse($resp);

        $resp = Validator::isXML(1);
        $this->assertFalse($resp);

        $resp = Validator::isXML(false);
        $this->assertFalse($resp);

        $resp = Validator::isXML('asdasds');
        $this->assertFalse($resp);
    }
}

<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\Signer;
use NFePHP\Common\SignerException;
use NFePHP\Common\Certificate;

class SignerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Signer::sign
     * @covers Signer::existsSignature
     * @covers Signer::createSignature
     * @covers Signer::canonize
     * @covers Signer::makeDigest
     */
    public function testSign()
    {
        $content = file_get_contents(__DIR__ . '/fixtures/xml/NFe/' .
            '35101158716523000119550010000000011003000000-nfe.xml');
        $pfx = file_get_contents(__DIR__ . '/fixtures/certs/certificado_teste.pfx');
        $certificate = Certificate::readPfx($pfx, 'associacao');
        $xmlsign = Signer::sign($certificate, $content, 'infNFe', 'Id');
        $actual = Signer::isSigned($xmlsign);
        $this->assertTrue($actual);
    }

    /**
     * @covers Signer::isSigned
     * @covers Signer::existsSignature
     * @covers Signer::digestCheck
     * @covers Signer::signatureCheck
     * @covers Signer::canonize
     * @covers Signer::makeDigest
     */
    public function testIsSigned()
    {
        $file = __DIR__ . '/fixtures/xml/NFe/35101158716523000119550010000000011003000000-nfeSigned.xml';
        $xml = file_get_contents($file);
        $actual = Signer::isSigned($xml);
        $this->assertTrue($actual);
    }

    /**
     * @covers Signer::existsSignature
     * @expectedException NFePHP\Common\Exception\SignerException
     */
    public function testSignFailNotXML()
    {
        $pfx = file_get_contents(__DIR__ . '/fixtures/certs/certificado_teste.pfx');
        $certificate = Certificate::readPfx($pfx, 'associacao');
        $content = "<html><body></body></html>";
        $xmlsign = Signer::sign($certificate, $content, 'infNFe', 'Id');
    }

    /**
     * @covers Signer::existsSignature
     * @covers Signer::digestCheck
     * @expectedException NFePHP\Common\Exception\SignerException
     */
    public function testIsSignedFailTagNotFound()
    {
        $file = __DIR__ . '/fixtures/xml/NFe/2017signed.xml';
        $xml = file_get_contents($file);
        $actual = Signer::isSigned($xml, 'infCTe');
    }

    /**
     * @covers Signer::existsSignature
     * @covers Signer::digestCheck
     * @covers Signer::canonize
     * @covers Signer::makeDigest
     * @expectedException NFePHP\Common\Exception\SignerException
     */
    public function testIsSignedFailDigest()
    {
        $file = __DIR__ . '/fixtures/xml/NFe/2017signedDigestFail.xml';
        $xml = file_get_contents($file);
        $actual = Signer::isSigned($xml);
    }

    /**
     * @covers Signer::existsSignature
     * @covers Signer::digestCheck
     * @covers Signer::signatureCheck
     * @covers Signer::canonize
     * @covers Signer::makeDigest
     * @expectedException NFePHP\Common\Exception\SignerException
     */
    public function testIsSignedFailSignature()
    {
        $file = __DIR__ . '/fixtures/xml/NFe/2017signedSignatureFail.xml';
        $xml = file_get_contents($file);
        $actual = Signer::isSigned($xml);
    }

    /**
     * @covers Signer::removeSignature
     * @covers Signer::existsSignature
     */
    public function testRemoveSignature()
    {
        $file = __DIR__ . '/fixtures/xml/NFe/nfeSignedFail.xml';
        $xml = file_get_contents($file);
        $nosigned = Signer::removeSignature($xml);
        $actual = Signer::isSigned($nosigned);
        $this->assertFalse($actual);
    }
}

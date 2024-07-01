<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\Signer;
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
     */
    public function testSignFailNotXML()
    {
        $this->expectException(\NFePHP\Common\Exception\SignerException::class);
        $pfx = file_get_contents(__DIR__ . '/fixtures/certs/certificado_teste.pfx');
        $certificate = Certificate::readPfx($pfx, 'associacao');
        $content = "<html><body></body></html>";
        Signer::sign($certificate, $content, 'infNFe', 'Id');
    }

    /**
     * @covers Signer::existsSignature
     * @covers Signer::digestCheck
     */
    public function testIsSignedFailTagNotFound()
    {
        $this->expectException(\NFePHP\Common\Exception\SignerException::class);
        $file = __DIR__ . '/fixtures/xml/NFe/2017signed.xml';
        $xml = file_get_contents($file);
        Signer::isSigned($xml, 'infCTe');
    }

    /**
     * @covers Signer::existsSignature
     * @covers Signer::digestCheck
     * @covers Signer::canonize
     * @covers Signer::makeDigest
     */
    public function testIsSignedFailDigest()
    {
        $this->expectException(\NFePHP\Common\Exception\SignerException::class);
        $file = __DIR__ . '/fixtures/xml/NFe/2017signedDigestFail.xml';
        $xml = file_get_contents($file);
        Signer::isSigned($xml);
    }

    public function testExistsSignatureRootnode(): void
    {
        $content = '<a><b><c></c><Signature></Signature></b></a>';
        $this->assertTrue(Signer::existsSignature($content));

        $content = '<a><b><c></c></b><Signature></Signature></a>';
        $this->assertTrue(Signer::existsSignature($content));

        $content = '<a><b><c></c><Signature></Signature></b></a>';
        $this->assertFalse(Signer::existsSignature($content, 'a'));

        $content = '<a><b><c></c></b><Signature></Signature></a>';
        $this->assertTrue(Signer::existsSignature($content, 'a'));

        $content = '<a><b><c></c><Signature></Signature></b></a>';
        $this->assertFalse(Signer::existsSignature($content, 'c'));

        $content = '<a><b><c><Signature></Signature></c></b></a>';
        $this->assertTrue(Signer::existsSignature($content, 'c'));

        $content = '<a><b><c></c><Signature></Signature></b></a>';
        $this->assertTrue(Signer::existsSignature($content, 'b'));
    }

    /**
     * @covers Signer::existsSignature
     * @covers Signer::digestCheck
     * @covers Signer::signatureCheck
     * @covers Signer::canonize
     * @covers Signer::makeDigest
     */
    public function testIsSignedFailSignature()
    {
        $this->expectException(\NFePHP\Common\Exception\SignerException::class);
        $file = __DIR__ . '/fixtures/xml/NFe/2017signedSignatureFail.xml';
        $xml = file_get_contents($file);
        Signer::isSigned($xml);
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

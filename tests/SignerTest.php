<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\Signer;
use NFePHP\Common\SignerException;
use NFePHP\Common\Certificate;
use DOMDocument;
use DOMNode;

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
        $doc = $this->getNFeDOMDocument();
        $node = $doc->getElementsByTagName('infNFe')->item(0);
        $certificate = $this->getCertificate();

        $xmlsign = Signer::sign($certificate, $doc, $node);
        $actual = Signer::isSigned($xmlsign->saveXml());

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
        $file = __DIR__ .'/fixtures/xml/NFe/35101158716523000119550010000000011003000000-nfeSigned.xml';
        $xml = file_get_contents($file);
        $actual = Signer::isSigned($xml);
        $this->assertTrue($actual);
    }
    
    /**
     * @covers Signer::existsSignature
     * @covers Signer::digestCheck
     * @expectedException NFePHP\Common\Exception\SignerException
     */
    public function testIsSignedFailTagNotFound()
    {
        $file = __DIR__ .'/fixtures/xml/NFe/2017signed.xml';
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
        $file = __DIR__ .'/fixtures/xml/NFe/2017signedDigestFail.xml';
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
        $file = __DIR__ .'/fixtures/xml/NFe/2017signedSignatureFail.xml';
        $xml = file_get_contents($file);
        $actual = Signer::isSigned($xml);
    }
    
    /**
     * @covers Signer::removeSignature
     * @covers Signer::existsSignature
     */
    public function testRemoveSignature()
    {
        $file = __DIR__ .'/fixtures/xml/NFe/nfeSignedFail.xml';
        $xml = file_get_contents($file);
        $nosigned = Signer::removeSignature($xml);
        $actual = Signer::isSigned($nosigned);
        $this->assertFalse($actual);
    }

    private static function getNFeDOMDocument(): DOMDocument
    {
        $content = file_get_contents(__DIR__. '/fixtures/xml/NFe/35101158716523000119550010000000011003000000-nfe.xml');

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;
        $doc->loadXML($content);

        return $doc;
    }

    private static function getCertificate(): Certificate
    {
        $pfx = file_get_contents(__DIR__. '/fixtures/certs/certificado_teste.pfx');
        $certificate = Certificate::readPfx($pfx, 'associacao');

        return $certificate;
    }
}

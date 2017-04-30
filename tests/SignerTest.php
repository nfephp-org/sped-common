<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\Signer;
use NFePHP\Common\SignerException;
use NFePHP\Common\Certificate;

class SignerTest extends \PHPUnit\Framework\TestCase
{
    public function testSign()
    {
        $content = file_get_contents(__DIR__. '/fixtures/xml/NFe/35101158716523000119550010000000011003000000-nfe.xml');
        $pfx = file_get_contents(__DIR__. '/fixtures/certs/certificado_teste.pfx');
        $certificate = Certificate::readPfx($pfx, 'associacao');
        $xmlsign = Signer::sign($certificate, $content, 'infNFe', 'Id');
        $dom = new \DOMDocument();
        $dom->loadXML($xmlsign);
        $actual = Signer::isSigned($dom, 'infNFe');
        $this->assertTrue($actual);
    }
    
    /**
     * @covers Signer::existsSignature
     * @covers Signer::digestCheck
     * @covers Signer::signatureCheck
     */
    public function testIsSigned()
    {
        $xml = __DIR__ .'/fixtures/xml/NFe/35101158716523000119550010000000011003000000-nfeSigned.xml';
        $dom = new \DOMDocument();
        $dom->load($xml);
        $actual = Signer::isSigned($dom, 'infNFe');
        $this->assertTrue($actual);
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testIsSignedFailTagNotFound()
    {
        $xml = __DIR__ .'/fixtures/xml/NFe/2017signed.xml';
        $dom = new \DOMDocument();
        $dom->load($xml);
        $actual = Signer::isSigned($dom, 'infCTe');
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testIsSignedFailDigest()
    {
        $xml = __DIR__ .'/fixtures/xml/NFe/2017signed.xml';
        $dom = new \DOMDocument();
        $dom->load($xml);
        $actual = Signer::isSigned($dom, 'infNFe');
    }
    
    /**
     * @expectedException \RuntimeException
     */
    public function testIsSignedFailSignature()
    {
        $xml = __DIR__ .'/fixtures/xml/NFe/nfeSignedFail.xml';
        $dom = new \DOMDocument();
        $dom->load($xml);
        $actual = Signer::isSigned($dom, 'infNFe');
    }
    
    public function testRemoveSignature()
    {
        $xml = __DIR__ .'/fixtures/xml/NFe/nfeSignedFail.xml';
        $dom = new \DOMDocument();
        $dom->load($xml);
        $nosigdom = Signer::removeSignature($dom);
        $actual = Signer::isSigned($nosigdom, 'infNFe');
        $this->assertFalse($actual);
    }
}

<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\Certificate;
use NFePHP\Common\SignerMulti;
use PHPUnit\Framework\TestCase;

class SignerMultiTest extends TestCase
{
    public function testSign(): void
    {
        $pfx = file_get_contents(__DIR__ . '/fixtures/certs/certificado_teste.pfx');
        $certificate = Certificate::readPfx($pfx, 'associacao');

        $xml = '<a>';
        $xml .=  '<b>';
        $xml .=     '<c>Teste Assinar mesmo documento 2x</c>';
        $xml .=  '</b>';
        $xml .= '</a>';

        $xmlSignTagB = SignerMulti::sign($certificate, $xml, 'b', 'Id', OPENSSL_ALGO_SHA1, SignerMulti::CANONICAL, 'b');
        $xmlsignTagA = SignerMulti::sign($certificate, $xmlSignTagB, 'b', 'Id', OPENSSL_ALGO_SHA1, SignerMulti::CANONICAL, 'a');

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadXML($xmlsignTagA);
        $this->assertEquals(2, $dom->getElementsByTagName('Signature')->count());
        $this->assertEquals('Signature', $dom->getElementsByTagName('a')->item(0)->childNodes->item(1)->nodeName);
        $this->assertEquals('Signature', $dom->getElementsByTagName('b')->item(0)->childNodes->item(1)->nodeName);
    }
}

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

        $xmlsign = SignerMulti::sign($certificate, '<a><b><c>Teste Assinar mesmo documento 2x</c></b></a>', 'b', 'Id', OPENSSL_ALGO_SHA1, SignerMulti::CANONICAL, 'b');
        $xmlsign2x = SignerMulti::sign($certificate, $xmlsign, 'b', 'Id', OPENSSL_ALGO_SHA1, SignerMulti::CANONICAL, 'a');
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadXML($xmlsign2x);
        $this->assertEquals(2, $dom->getElementsByTagName('Signature')->count());
    }
}

<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate;
use NFePHP\Common\Exception\CertificateException;
use NFePHP\Common\Signer;

class SignerTest extends \PHPUnit\Framework\TestCase
{
    const TEST_PFX_FILE = '/../fixtures/certs/certificado_teste.pfx';
    
    public function testShouldLoadSignner()
    {
        $certificate = Certificate::readPfx(file_get_contents(__DIR__ . self::TEST_PFX_FILE), 'associacao');
        $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR  . '../fixtures/xml/NFe/35101158716523000119550010000000011003000000-nfe.xml');
        $tagname = 'infNFe';
        $mark = 'Id';
        $actual = Signer::sign($certificate, $content, $tagname, $mark);
        $expected = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR  . '../fixtures/xml/NFe/35101158716523000119550010000000011003000000-nfeSigned.xml');
        $this->assertEquals($expected, $actual);
    }
}

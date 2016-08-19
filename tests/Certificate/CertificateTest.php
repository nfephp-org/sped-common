<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate;

class CertificateTest extends \PHPUnit_Framework_TestCase
{
    const TEST_CERTIFICATE_FILE = '/../fixtures/certs/certificado_teste.pfx';

    public function testShouldLoadPfxCertificate()
    {
        $certificate = new Certificate(file_get_contents(__DIR__ . self::TEST_CERTIFICATE_FILE), 'associacao');
        $this->assertInstanceOf(Certificate::class, $certificate);
        $this->assertEquals('Teste Projeto NFe RS', $certificate->companyName);
        $this->assertEquals(new \DateTime('2009-05-22 17:07:03'), $certificate->validFrom);
        $this->assertEquals(new \DateTime('2010-10-02 17:07:03'), $certificate->validTo);
        $this->assertFalse($certificate->isExpired());
        $this->assertNotEmpty($certificate->encrypt('nfe'));
    }
}

<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate;
use NFePHP\Common\Exception\CertificateException;

class CertificateTest extends \PHPUnit_Framework_TestCase
{
    const TEST_CERTIFICATE_FILE = '/../fixtures/certs/certificado_teste.pfx';

    public function testShouldLoadPfxCertificate()
    {
        $certificate = new Certificate(file_get_contents(__DIR__ . self::TEST_CERTIFICATE_FILE), 'associacao');
        $this->assertInstanceOf(Certificate::class, $certificate);
        $this->assertEquals('NFe - Associacao NF-e:99999090910270', $certificate->getCompanyName());
        $this->assertEquals(new \DateTime('2009-05-22 17:07:03'), $certificate->getValidFrom());
        $this->assertEquals(new \DateTime('2010-10-02 17:07:03'), $certificate->getValidTo());
        $this->assertFalse($certificate->isExpired());
        $dataSigned = $certificate->sign('nfe');
        $this->assertTrue($certificate->verify('nfe', $dataSigned));
    }

    public function testShouldGetExceptionWhenLoadPfxCertificate()
    {
        $this->setExpectedException(CertificateException::class);
        new Certificate(file_get_contents(__DIR__ . self::TEST_CERTIFICATE_FILE), 'error');
    }
}

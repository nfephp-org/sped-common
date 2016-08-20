<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate;
use NFePHP\Common\Exception\CertificateException;

class CertificateTest extends \PHPUnit_Framework_TestCase
{
    const TEST_PFX_FILE = '/../fixtures/certs/certificado_teste.pfx';

    const TEST_PRIVATE_KEY = '/../fixtures/certs/x99999090910270_priKEY.pem';

    const TEST_PUBLIC_KEY = '/../fixtures/certs/x99999090910270_pubKEY.pem';

    public function testShouldLoadPfxCertificate()
    {
        $certificate = Certificate::readPfx(file_get_contents(__DIR__ . self::TEST_PFX_FILE), 'associacao');
        $this->assertEquals('NFe - Associacao NF-e:99999090910270', $certificate->getCompanyName());
        $this->assertEquals(new \DateTime('2009-05-22 17:07:03'), $certificate->getValidFrom());
        $this->assertEquals(new \DateTime('2010-10-02 17:07:03'), $certificate->getValidTo());
        $this->assertTrue($certificate->isExpired());
        $dataSigned = $certificate->sign('nfe');
        $this->assertTrue($certificate->verify('nfe', $dataSigned));
    }

    public function testShouldLoadCertificate()
    {
        $certificate = new Certificate(
            new Certificate\PrivateKey(file_get_contents(__DIR__ . self::TEST_PRIVATE_KEY)),
            new Certificate\PublicKey(file_get_contents(__DIR__ . self::TEST_PUBLIC_KEY))
        );
        $this->assertInstanceOf(Certificate::class, $certificate);
        $this->assertEquals('NFe - Associacao NF-e:99999090910270', $certificate->getCompanyName());
        $this->assertEquals(new \DateTime('2009-05-22 17:07:03'), $certificate->getValidFrom());
        $this->assertEquals(new \DateTime('2010-10-02 17:07:03'), $certificate->getValidTo());
        $this->assertTrue($certificate->isExpired());
        $dataSigned = $certificate->sign('nfe');
        $this->assertTrue($certificate->verify('nfe', $dataSigned));
    }

    public function testShouldGetExceptionWhenLoadPfxCertificate()
    {
        $this->setExpectedException(CertificateException::class);
        Certificate::readPfx(file_get_contents(__DIR__ . self::TEST_PFX_FILE), 'error');
    }
}

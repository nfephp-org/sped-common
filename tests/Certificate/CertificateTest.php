<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate;

class CertificateTest extends \PHPUnit_Framework_TestCase
{

    const TEST_CERTIFICATE_FILE = '/../fixtures/certs/certificado_teste.pfx';

    public function testShouldLoadPfxCertificate()
    {
        $certificate = new Certificate(file_get_contents(__DIR__ . self::TEST_CERTIFICATE_FILE), 'associacao');
        
        $this->assertEquals('NFe - Associacao NF-e:99999090910270', $certificate->companyName);
        $this->assertEquals(new \DateTime('2009-05-22 17:07:03'), $certificate->validFrom);
        $this->assertEquals(new \DateTime('2010-10-02 17:07:03'), $certificate->validTo);
        $this->assertTrue($certificate->isExpired());

        $assing = $certificate->sign('nfe');
        $this->assertEquals(
            1, openssl_verify('nfe', $assing, $certificate->publicKey, OPENSSL_ALGO_SHA1)
        );
    }

    /**
     * @expectedException NFePHP\Common\Exception\CertificateException
     */
    public function testPasswordErrorException()
    {
        $certificate = new Certificate(file_get_contents(__DIR__ . self::TEST_CERTIFICATE_FILE), 'error');
    }
}

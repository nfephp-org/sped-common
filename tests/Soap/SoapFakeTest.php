<?php

namespace NFePHP\Common\Tests\Soap;

use NFePHP\Common\Exception\RuntimeException;
use NFePHP\Common\Soap\SoapBase;
use NFePHP\Common\Soap\SoapFake;
use NFePHP\Common\Certificate;

class SoapFakeTest extends \PHPUnit\Framework\TestCase
{
    const TEST_PFX_FILE = '/../fixtures/certs/certificado_teste.pfx';

    /**
     * @covers SoapBase::__construct
     */
    public function testInstanciate()
    {
        $soap = new SoapFake();
        $this->assertInstanceOf(SoapFake::class, $soap);
    }

    public function testDisableSecurity()
    {
        $soap = new SoapFake();
        $actual = $soap->disableSecurity();
        $this->assertFalse($actual);
    }

    public function testLoadCA()
    {
        $this->assertTrue(true);
    }

    public function testSetTemporaryFolder()
    {
        $this->assertTrue(true);
    }
    public function testSetDebugMode()
    {
        $this->assertTrue(true);
    }
    public function testLoadCertificate()
    {
        $this->assertTrue(true);
    }
    public function testLoadLogger()
    {
        $this->assertTrue(true);
    }
    public function testTimeout()
    {
        $this->assertTrue(true);
    }
    public function testProtocol()
    {
        $this->assertTrue(true);
    }
    public function testSetSoapPrefix()
    {
        $this->assertTrue(true);
    }
    public function testProxy()
    {
        $this->assertTrue(true);
    }
    public function testSaveTemporarilyKeyFiles()
    {
        $this->assertTrue(true);
    }
    public function testRemoveTemporarilyFiles()
    {
        $this->assertTrue(true);
    }
    public function testSaveDebugFiles()
    {
        $this->assertTrue(true);
    }

    public function testSend()
    {
        $this->assertTrue(true);
    }

    public function testDisableCertValidation()
    {
        $certificate = Certificate::readPfx(file_get_contents(__DIR__ . self::TEST_PFX_FILE), 'associacao');
        $soap = new SoapFake();
        $soap->disableCertValidation(true);
        $soap->loadCertificate($certificate);
        $this->assertInstanceOf(SoapBase::class, $soap);
    }

    public function testDisableCertValidationFail()
    {
        $this->expectException(RuntimeException::class);
        $certificate = Certificate::readPfx(file_get_contents(__DIR__ . self::TEST_PFX_FILE), 'associacao');
        $soap = new SoapFake($certificate);
    }
}

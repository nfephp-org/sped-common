<?php

namespace NFePHP\Common\Tests\Soap;

use NFePHP\Common\Soap\SoapFake;

class SoapFakeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers SoapBase::__construct
     */
    public function testInstanciate()
    {
        $soap = new SoapFake();
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
}

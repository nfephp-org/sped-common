<?php

namespace NFePHP\Common\Tests\Soap;

use NFePHP\Common\Soap\SoapNative;

class SoapNativeTest extends \PHPUnit\Framework\TestCase
{
    
    public function testInstanciate()
    {
        $soap = new SoapNative();
    }
   
    public function testSend()
    {
        $this->assertTrue(true);
    }
}

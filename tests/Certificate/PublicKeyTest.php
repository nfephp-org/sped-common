<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate\PublicKey;
use NFePHP\Common\Certificate\VerificationInterface;

class PublicKeyTest extends \PHPUnit_Framework_TestCase
{
    const TEST_PUBLIC_KEY = '/../fixtures/certs/x99999090910270_pubKEY.pem';
    protected $key;
    
    public function __construct()
    {
        $this->key = new PublicKey(file_get_contents(__DIR__ . self::TEST_PUBLIC_KEY));
    }
    
    public function testShouldInstantiate()
    {
        $this->assertInstanceOf(VerificationInterface::class, $this->key);
        $this->assertEquals('NFe - Associacao NF-e:99999090910270', $this->key->commonName);
        $this->assertEquals(new \DateTime('2009-05-22 17:07:03'), $this->key->validFrom);
        $this->assertEquals(new \DateTime('2010-10-02 17:07:03'), $this->key->validTo);
        $this->assertTrue($this->key->isExpired());
    }
    
    public function testUnFormated()
    {
        $actual = $this->key->unFormated();
        $expected  = preg_replace('/-----.*[\n]?/', '', file_get_contents(__DIR__ . self::TEST_PUBLIC_KEY));
        $expected  = preg_replace('/[\n\r]/', '', $expected);
        $this->assertEquals($expected, $actual);
    }
}

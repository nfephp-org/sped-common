<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate\PublicKey;
use NFePHP\Common\Certificate\VerificationInterface;

class PublicKeyTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldInstantiate()
    {
        $key = new PublicKey(file_get_contents(__DIR__ . '/../fixtures/certs/x99999090910270_pubKEY.pem'));
        $this->assertInstanceOf(VerificationInterface::class, $key);
        $this->assertEquals('NFe - Associacao NF-e:99999090910270', $key->commonName);
        $this->assertEquals(new \DateTime('2009-05-22 17:07:03'), $key->validFrom);
        $this->assertEquals(new \DateTime('2010-10-02 17:07:03'), $key->validTo);
        $this->assertFalse($key->isExpired());
    }
}

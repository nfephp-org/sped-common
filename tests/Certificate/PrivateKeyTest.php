<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate\PrivateKey;
use NFePHP\Common\Certificate\SignatureInterface;

class PrivateKeyTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldInstantiate()
    {
        $key = new PrivateKey(file_get_contents(__DIR__ . '/../fixtures/certs/x99999090910270_priKEY.pem'));
        $this->assertInstanceOf(SignatureInterface::class, $key);
        $this->assertNotNull($key->sign('nfe'));
    }
}

<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate\PrivateKey;
use NFePHP\Common\Certificate\SignatureInterface;

class PrivateKeyTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldInstantiate()
    {
        $key = new PrivateKey(file_get_contents(__DIR__ . '/../fixtures/certs/x99999090910270_priKEY.pem'));
        $this->assertInstanceOf(SignatureInterface::class, $key);
        $this->assertNotNull($key->sign('nfe'));
    }

    /**
     * @covers PrivateKey::read
     */
    public function testSign()
    {
        $expected = 'Ih6ofm3m55hmzFab24VH2Be8dQGiQHkj9AV89YJwIu5pA1DU5IX6UKZOJuDv8sKmNdkkdSwJCeitFGSTx01aMSPn50Naj' .
            '/VL0fva4JRNYnKxoIKd87hGlYbv2l9cCPmopx1bG7SV6i9PDqx4RNoopw4jhr+u1sEq49/nLQ78tdQ=';
        $key = new PrivateKey(file_get_contents(__DIR__ . '/../fixtures/certs/x99999090910270_priKEY.pem'));
        $content = "conteudo a ser assinado";
        $actual = base64_encode($key->sign($content, OPENSSL_ALGO_SHA1));
        $this->assertEquals($expected, $actual);
    }


    public function testToString()
    {
        $expected = file_get_contents(__DIR__ . '/../fixtures/certs/x99999090910270_priKEY.pem');
        $key = new PrivateKey($expected);
        $this->assertEquals($expected, "{$key}");
    }
}

<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate\Asn1;

class Asn1Test extends \PHPUnit\Framework\TestCase
{
    const TEST_PUBLIC_KEY = '/../fixtures/certs/x99999090910270_pubKEY.pem';

    public function testGetCNPJ()
    {
        $expected = '99999090910270';
        $actual = Asn1::getCNPJ(file_get_contents(__DIR__ . self::TEST_PUBLIC_KEY));
        $this->assertEquals($expected, $actual);
    }
}

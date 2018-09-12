<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\Keys;

class KeysTest extends \PHPUnit\Framework\TestCase
{
    public function testBuildWithCod()
    {
        $cUF = 35;
        $ano = 17;
        $mes = 4;
        $cnpj = '58716523000119';
        $mod = 55;
        $serie = 1;
        $numero = 12;
        $tpEmis = 1;
        $codigo = '12345';
        $key = Keys::build($cUF, $ano, $mes, $cnpj, $mod, $serie, $numero, $tpEmis, $codigo);
        $this->assertEquals($key, '35170458716523000119550010000000121000123458');
    }
    
    public function testBuildWithCPF()
    {
        $cUF = 35;
        $ano = 17;
        $mes = 4;
        $cnpj = '99999999999';
        $mod = 55;
        $serie = 1;
        $numero = 12;
        $tpEmis = 1;
        $codigo = '12345';
        $key = Keys::build($cUF, $ano, $mes, $cnpj, $mod, $serie, $numero, $tpEmis, $codigo);
        $this->assertEquals($key, '35170400099999999999550010000000121000123459');
    }
    
    public function testIsValidTrue()
    {
        $key = "35170358716523000119550010000000301000000300";
        $actual = Keys::isValid($key);
        $this->assertTrue($actual);
    }
    
    public function testIsValidFalse()
    {
        $key = "35170358716523000119550010000000301000000306";
        $actual = Keys::isValid($key);
        $this->assertFalse($actual);
    }    
    
    public function testVerifyingDigit()
    {
        $key = "3517035871652300011955001000000030100000030";
        $actual = Keys::verifyingDigit($key);
        $expected = '0';
        $this->assertEquals($expected, $actual);
    }
    
    public function testVerifyingDigitEmpty()
    {
        $key = "3517035871652300011955001000000030100";
        $actual = Keys::verifyingDigit($key);
        $expected = '';
        $this->assertEquals($expected, $actual);
    }
}

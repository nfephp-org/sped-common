<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\UFList;

class UFListTest extends \PHPUnit\Framework\TestCase
{
    public $uflist = [
        12=>'AC',
        27=>'AL',
        13=>'AM',
        91=>'AN',
        16=>'AP',
        29=>'BA',
        23=>'CE',
        53=>'DF',
        32=>'ES',
        52=>'GO',
        21=>'MA',
        31=>'MG',
        50=>'MS',
        51=>'MT',
        15=>'PA',
        25=>'PB',
        26=>'PE',
        22=>'PI',
        41=>'PR',
        33=>'RJ',
        24=>'RN',
        11=>'RO',
        14=>'RR',
        43=>'RS',
        42=>'SC',
        28=>'SE',
        35=>'SP',
        17=>'TO',
        92=>'SVCAN',
        93=>'SVCRS'
    ];

    public function testgetUFByCode()
    {
        $uf = UFList::getUFByCode(35);
        $this->assertEquals('SP', $uf);
    }

    public function testgetUFByCodeFail()
    {
        $this->expectException(\InvalidArgumentException::class);
        $uf = UFList::getUFByCode(77);
    }

    public function testgetUFByUF()
    {
        $code = UFList::getCodeByUF('Sp');
        $this->assertEquals(35, $code);
    }

    public function testgetUFByUFFail()
    {
        $this->expectException(\InvalidArgumentException::class);
        $code = UFList::getCodeByUF('aa');
    }

    public function testGetListByUF()
    {
        $actual = UFList::getListByUF();
        $expected = array_flip($this->uflist);
        $this->assertEquals($expected, $actual);
    }

    public function testGetListByCode()
    {
        $actual = UFList::getListByCode();
        $expected = $this->uflist;
        $this->assertEquals($expected, $actual);
    }
}

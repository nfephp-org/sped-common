<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\TimeZoneByUF;

class TimeZoneByUFTest extends \PHPUnit_Framework_TestCase
{
    public function testget()
    {
        $tzd = TimeZoneByUF::get('SP');
        $this->assertEquals('America/Sao_Paulo', $tzd);
    }

    public function testgetByCode()
    {
        $tzd = TimeZoneByUF::get(35);
        $this->assertEquals('America/Sao_Paulo', $tzd);
    }
}

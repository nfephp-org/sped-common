<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\UFList;

class UFListTest extends \PHPUnit_Framework_TestCase
{
    public function testgetUFByCode()
    {
        $uf = UFList::getUFByCode(35);
        $this->assertEquals('SP', $uf);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testgetUFByCodeFail()
    {
        $uf = UFList::getUFByCode(77);
    }
    
    public function testgetUFByUF()
    {
        $code = UFList::getCodeByUF('Sp');
        $this->assertEquals(35, $code);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testgetUFByUFFail()
    {
        $code = UFList::getCodeByUF('aa');
    }
    
    
}

<?php

namespace NFePHP\Common\Tests;

use NFePHP\Common\DOMImproved;

class DomImprovedTest  extends \PHPUnit\Framework\TestCase
{
    public function testInstanciate()
    {
        $dom = new DOMImproved();
        $this->assertInstanceOf(DOMImproved::class, $dom);
    }
    
    public function testAddArrayChild()
    {
        $this->assertTrue(true);
    }
    public function testAddChild()
    {
        $this->assertTrue(true);
    }
    public function testAppChild()
    {
        $this->assertTrue(true);
    }
    public function testAppChildBefore()
    {
        $this->assertTrue(true);
    }
    public function testAppExternalChild()
    {
        $this->assertTrue(true);
    }
    public function testAppExternalChildBefore()
    {
        $this->assertTrue(true);
    }
    public function testGetChave()
    {
        $this->assertTrue(true);
    }
    public function testGetNode()
    {
        $this->assertTrue(true);
    }
    public function testGetNodeValue()
    {
        $this->assertTrue(true);
    }
    public function testGetValue()
    {
        $this->assertTrue(true);
    }
    public function testLoadXMLFile()
    {
        $this->assertTrue(true);
    }
    public function testLoadXMLString()
    {
        $this->assertTrue(true);
    }
}

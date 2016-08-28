<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate\ChainKeys;

class ChainKeysTest extends \PHPUnit_Framework_TestCase
{
    const TEST_CHAIN_KEYS = '/../fixtures/certs/chain.pem';
    
    public function testShouldInstantiate()
    {
        $chain = new ChainKeys();
        $this->assertInstanceOf(ChainKeys::class, $chain);
        $chain->add(file_get_contents(__DIR__ . '/../fixtures/certs/ACCertisignG6_v2.cer'));
        $list = $chain->listChain();
        $this->assertEquals('2021-09-20', $list['AC Certisign G6']['validTo']);
        $chain->add(file_get_contents(__DIR__ . '/../fixtures/certs/ACCertisignMultiplaG5.cer'));
        $chain->add(file_get_contents(__DIR__ . '/../fixtures/certs/ACRaizBrasileira_v2.cer'));
        $list = $chain->listChain();
        $this->assertEquals(3, count($list));
    }
    
    public function testShouldInstantiateConstruct()
    {
        $chain = new ChainKeys(file_get_contents(__DIR__ . self::TEST_CHAIN_KEYS));
        $this->assertInstanceOf(ChainKeys::class, $chain);
        $list = $chain->listChain();
        $this->assertEquals(3, count($list));
    }
}

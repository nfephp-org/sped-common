<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate\CertificationChain;

class CertificationChainTest extends \PHPUnit\Framework\TestCase
{
    const TEST_CHAIN_KEYS = '/../fixtures/certs/chain.pem';
    
    public function testShouldInstantiate()
    {
        $chain = new CertificationChain();
        $this->assertInstanceOf(CertificationChain::class, $chain);
        $chain->add(file_get_contents(__DIR__ . '/../fixtures/certs/ACCertisignG6_v2.cer'));
        $list = $chain->listChain();
        $publickey = $list['AC Certisign G6'];
        $this->assertEquals('2021-09-20', $publickey->validTo->format('Y-m-d'));
        $chain->add(file_get_contents(__DIR__ . '/../fixtures/certs/ACCertisignMultiplaG5.cer'));
        $chain->add(file_get_contents(__DIR__ . '/../fixtures/certs/ACRaizBrasileira_v2.cer'));
        $list = $chain->listChain();
        $this->assertEquals(3, count($list));
    }
    
    public function testShouldInstantiateConstruct()
    {
        $chain = new CertificationChain(file_get_contents(__DIR__ . self::TEST_CHAIN_KEYS));
        $this->assertInstanceOf(CertificationChain::class, $chain);
        $list = $chain->listChain();
        $this->assertEquals(3, count($list));
    }
    
    public function testCanLoadCertificateDER()
    {
        $chain1 = file_get_contents(__DIR__ . '/../fixtures/certs/ACCertisignG7.cer');
        $chain2 = file_get_contents(__DIR__ . '/../fixtures/certs/ACCertisignMultiplaG7.cer');
        $chain3 = file_get_contents(__DIR__ . '/../fixtures/certs/ACRaizV5.cer');
        $chain = new CertificationChain();
        $chain->add($chain1);
        $chain->add($chain2);
        $chain->add($chain3);
        $list = $chain->listChain();
        $this->assertEquals(3, count($list));
    }
}

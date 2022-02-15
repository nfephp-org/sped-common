<?php

namespace NFePHP\Common\Tests\Certificate;

use NFePHP\Common\Certificate\PublicKey;
use NFePHP\Common\Certificate\VerificationInterface;

class PublicKeyTest extends \PHPUnit\Framework\TestCase
{
    const TEST_PUBLIC_KEY = '/../fixtures/certs/x99999090910270_pubKEY.pem';

    protected $key;

    public function setUp(): void
    {
        $this->key = new PublicKey(file_get_contents(__DIR__ . self::TEST_PUBLIC_KEY));
    }

    /**
     * @covers PublicKey::read
     */
    public function testShouldInstantiate()
    {
        $this->assertInstanceOf(VerificationInterface::class, $this->key);
        $this->assertEquals('NFe - Associacao NF-e:99999090910270', $this->key->commonName);
        $this->assertEquals(new \DateTime('2009-05-22 17:07:03'), $this->key->validFrom);
        $this->assertEquals(new \DateTime('2010-10-02 17:07:03'), $this->key->validTo);
        $this->assertTrue($this->key->isExpired());
    }

    public function testUnFormatted()
    {
        $actual = $this->key->unFormated();
        $expected = preg_replace('/-----.*[\n]?/', '', file_get_contents(__DIR__ . self::TEST_PUBLIC_KEY));
        $expected = preg_replace('/[\n\r]/', '', $expected);
        $this->assertEquals($expected, $actual);
    }

    public function testShouldCreateFromContent()
    {
        $content = preg_replace('/-----.*[\n]?/', '', file_get_contents(__DIR__ . self::TEST_PUBLIC_KEY));
        $key = PublicKey::createFromContent($content);
        $this->assertEquals($key, $this->key);
    }

    public function testGetCNPJ()
    {
        $expected = '99999090910270';
        $actual = $this->key->cnpj();
        $this->assertEquals($expected, $actual);
    }

    public function testGetNonExistCPF()
    {
        $expected = null;
        $actual = $this->key->cpf();
        $this->assertEquals($expected, $actual);
    }

    public function testGetCPF()
    {
        $key = new PublicKey(file_get_contents(__DIR__ . '/../fixtures/certs/e-CPF_pubkey.pem'));
        $expected = '80767940130';
        $actual = $key->cpf();
        $this->assertEquals($expected, $actual);
    }

    public function testVerify()
    {
        $dom = new \DOMDocument();
        $dom->load(__DIR__ . '/../fixtures/xml/NFe/nfeSignedFail.xml');
        $signature = $dom->getElementsByTagName('Signature')->item(0);
        $sigMethAlgo = $signature->getElementsByTagName('SignatureMethod')->item(0)->getAttribute('Algorithm');
        if ($sigMethAlgo == 'http://www.w3.org/2000/09/xmldsig#rsa-sha1') {
            $algorithm = OPENSSL_ALGO_SHA1;
        } else {
            $algorithm = OPENSSL_ALGO_SHA256;
        }
        $certificateContent = $signature->getElementsByTagName('X509Certificate')->item(0)->nodeValue;
        $publicKey = PublicKey::createFromContent($certificateContent);
        $signContent = $signature->getElementsByTagName('SignedInfo')->item(0)->C14N(true, false);
        $signatureValue = $signature->getElementsByTagName('SignatureValue')->item(0)->nodeValue;
        $decodedSignature = base64_decode(str_replace(array("\r", "\n"), '', $signatureValue));
        $actual = $publicKey->verify($signContent, $decodedSignature, $algorithm);
        $this->assertFalse($actual);
    }
}

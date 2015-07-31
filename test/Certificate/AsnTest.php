<?php

namespace SpedTest\Common;

/**
 * Class AsnTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use Sped\Common\Certificate\Asn;

if (!defined('TEST_ROOT')) {
    define('TEST_ROOT', dirname(dirname(__FILE__)));
}

class AsnTest extends \PHPUnit_Framework_TestCase
{
    public function testConsigoPegarCNPJ()
    {
        $certificado = TEST_ROOT . '/fixtures/certs/certificado_pubKEY.pem';
        $certPem = file_get_contents($certificado);
        $cnpj = Asn::getCNPJCert($certPem);
        $this->assertEquals($cnpj, '99999090910270');
    }
}

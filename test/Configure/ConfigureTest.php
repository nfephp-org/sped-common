<?php

namespace SpedTest\Common;

/**
 * Class ConfigureTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use Sped\Common\Configure\Configure;

if (!defined('TEST_ROOT')) {
    define('TEST_ROOT', dirname(dirname(__FILE__)));
}

class ConfigureTest extends \PHPUnit_Framework_TestCase
{
    public function testeInstanciar()
    {
        $cnpj = '99999090910270';
        $pathCertsFiles = TEST_ROOT . '/fixtures/certs/certificado_teste.pfx';
        $certPfxName = 'certificado_teste.pfx';
        $certPassword = 'associacao';
        $aResp = Configure::checkCerts($cnpj, $pathCertsFiles, $certPfxName, $certPassword);
    }
}

<?php

namespace SpedTest\Common;

/**
 * Class LotNumberTest
 * @author Roberto L. Machado <linux dot rlm at gmail dot com>
 */

if (!defined('TEST_ROOT')) {
    define('TEST_ROOT', dirname(dirname(__FILE__)));
}

use Sped\Common\Lots\LotNumber;

class LotNumberTest extends \PHPUnit_Framework_TestCase
{
    public function testGeraNumLote()
    {
        $numLote = (string) LotNumber::geraNumLote(15);
        $num = strlen($numLote);
        $this->assertEquals($num, 15);
    }
}

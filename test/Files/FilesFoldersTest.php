<?php

namespace SpedTest\Common;

/**
 * Class FilesFoldersTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use Sped\Common\Files\FilesFolders;

if (!defined('TEST_ROOT')) {
    define('TEST_ROOT', dirname(dirname(__FILE__)));
}

class FilesFoldersTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFoldersSuccess()
    {
        $folderBase = TEST_ROOT . '/fixtures/NFe';
        $resp = FilesFolders::createFolders($folderBase);
        $this->assertTrue($resp);
        $resp = FilesFolders::removeFolder($folderBase);
        $this->assertTrue($resp);
    }

     /**
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage mkdir(): Permission denied
     */
    public function testCreateFoldersFail()
    {
        $folderBase = '/root';
        FilesFolders::createFolders($folderBase);
    }
    
    public function testListDirSuccess()
    {
        $files = array(
            TEST_ROOT . '/fixtures/certs/99999090910270_certKEY.pem',
            TEST_ROOT . '/fixtures/certs/99999090910270_priKEY.pem',
            TEST_ROOT . '/fixtures/certs/99999090910270_pubKEY.pem',
            TEST_ROOT . '/fixtures/certs/certificado_pubKEY.pem'
        );
        $folderBase = TEST_ROOT . '/fixtures/certs/';
        $aList = FilesFolders::listDir($folderBase, '*.pem', true);
        $this->assertEquals($aList, $files);
    }
    
    /**
     * @expectedException Sped\Common\Exception\InvalidArgumentException
     * @expectedExceptionMessage O diretório não existe /qualquercoisa !!!
     */
    public function testListDirFail()
    {
        $aList = array();
        $folderBase = '/qualquercoisa';
        $files = FilesFolders::listDir($folderBase, '*.*', false);
        $this->assertEquals($aList, $files);
    }
            
    public function testWriteTest()
    {
        $htmlStandard = '<tr bgcolor="#FFFFCC">'
                . '<td>Test</td>'
                . '<td bgcolor="#00CC00">'
                . '<div align="center"> Permiss&atilde;o OK</div>'
                . '</td>'
                . '<td>O diret&oacute;rio deve ter permiss&atilde;o de escrita</td>'
                . '</tr>';
        $folderBase = TEST_ROOT . '/fixtures/certs';
        $respHtml = '';
        $resp = FilesFolders::writeTest($folderBase, 'Test', $respHtml);
        $this->assertTrue($resp);
        $this->assertEquals($htmlStandard, $respHtml);
    }
}

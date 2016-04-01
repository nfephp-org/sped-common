<?php

/**
 * Class FilesFoldersTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\Common\Files\FilesFolders;

class FilesFoldersTest extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        
    }
    
    public function testCreateFoldersSuccess()
    {
        $folderBase = dirname(dirname(__FILE__)) . '/fixtures/NFe';
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
            dirname(dirname(__FILE__)) . '/fixtures/certs/certificado_pubKEY.pem',
            dirname(dirname(__FILE__)) . '/fixtures/certs/x99999090910270_certKEY.pem',
            dirname(dirname(__FILE__)) . '/fixtures/certs/x99999090910270_priKEY.pem',
            dirname(dirname(__FILE__)) . '/fixtures/certs/x99999090910270_pubKEY.pem'
        );
        $folderBase = dirname(dirname(__FILE__)) . '/fixtures/certs/';
        $aList = FilesFolders::listDir($folderBase, '*.pem', true);
        $this->assertEquals($aList, $files);
    }
    
    /**
     * @expectedException NFePHP\Common\Exception\InvalidArgumentException
     * @expectedExceptionMessage O diretório não existe /qualquercoisa !!!
     */
    public function testListDirFail()
    {
        $aList = array();
        $folderBase = '/qualquercoisa';
        $files = FilesFolders::listDir($folderBase, '*.*', false);
        $this->assertEquals($aList, $files);
    }
}

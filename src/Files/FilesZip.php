<?php

namespace Sped\Common\Files;

/**
 * Classe auxiliar para compactar e descompactar strings
 * @category   NFePHP
 * @package    Sped\Common\Files
 * @copyright  Copyright (c) 2008-2014
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use Sped\Common\Exception;

class FilesZip
{
    /**
     * compacta uma string usando Gzip
     * @param string $data
     * @return string
     */
    public static function gZipString($data = '')
    {
        return gzencode($data, 9, FORCE_GZIP);
    }
    
    /**
     * descompacta uma string usando Gzip
     * @param string $data
     * @return string
     */
    public static function unGZipString($data = '')
    {
        return gzdecode($data);
    }
    
    /**
     * compacta uma string usando ZLIB
     * @param string $data
     * @return string
     */
    public static function zipString($data = '')
    {
        return gzcompress($data, 9);
    }
}

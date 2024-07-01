<?php

/**
 * Class to signner a Xml
 * Meets packages :
 *     sped-nfe,
 *     sped-cte,
 *     sped-mdfe,
 *     sped-nfse,
 *     sped-efinanceira
 *     sped-esocial
 *     sped-efdreinf
 *     e sped-esfinge
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Signer
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-common for the canonical source repository
 */

namespace NFePHP\Common;

class SignerMulti extends Signer
{
    /**
     * @param string $content
     * @return false
     */
    public static function existsSignature($content)
    {
        return false;
    }
}

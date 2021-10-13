<?php

namespace NFePHP\Common\Exception;

/**
 * @category   NFePHP
 * @package    NFePHP\Common\Exception
 * @copyright  Copyright (c) 2008-2014
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

class SignerException extends \RuntimeException implements ExceptionInterface
{
    public static function isNotXml()
    {
        return new static('O conteudo não é um XML válido.');
    }

    public static function digestComparisonFailed()
    {
        return new static('O conteúdo do XML não corresponde ao Digest Value. '
           . 'Provavelmente foi alterado após ter sido assinado');
    }

    public static function signatureComparisonFailed()
    {
        return new static('A assinatura do XML não combina. '
           . 'O conteúdo provavelmente foi alterado após ter sido assinado.');
    }


    public static function tagNotFound($tagname)
    {
        return new static("A tag especificada &lt;$tagname&gt; não foi localizada no xml.");
    }
}

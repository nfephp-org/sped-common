<?php

namespace NFePHP\Common;

/**
 * @category   NFePHP
 * @package    NFePHP\Common\Validator
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use NFePHP\Common\Exception\ValidatorException;
use DOMDocument;

class Validator
{
    /**
     * Find erros in XML validate with schema XSD
     * @param string $xml XML content
     * @param string $xsd real path to scheme file
     * @return boolean
     * @throws NFePHP\Common\Exception\ValidatorException
     */
    public static function isValid($xml, $xsd)
    {
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        libxml_clear_errors();
        $errors = [];
        if (! $dom->schemaValidate($xsd)) {
            $aErrors = libxml_get_errors();
            foreach ($aErrors as $error) {
                $errors[] = self::translateError($error->message);
            }
            throw ValidatorException::errorsFound($errors);
        }
        return true;
    }
    
    /**
     * Translate errors to portuguese
     * @param string $msg
     * @return string
     */
    protected static function translateError($msg)
    {
        $enErr = array(
            "{http://www.portalfiscal.inf.br/nfe}",
            "[facet 'pattern']",
            "The value",
            "is not accepted by the pattern",
            "has a length of",
            "[facet 'minLength']",
            "this underruns the allowed minimum length of",
            "[facet 'maxLength']",
            "this exceeds the allowed maximum length of",
            "Element",
            "attribute",
            "is not a valid value of the local atomic type",
            "is not a valid value of the atomic type",
            "Missing child element(s). Expected is",
            "The document has no document element",
            "[facet 'enumeration']",
            "one of",
            "failed to load external entity",
            "Failed to locate the main schema resource at",
            "This element is not expected. Expected is",
            "is not an element of the set"
        );

        $ptErr = array(
            "",
            "[Erro 'Layout']",
            "O valor",
            "não é aceito para o padrão.",
            "tem o tamanho",
            "[Erro 'Tam. Min']",
            "deve ter o tamanho mínimo de",
            "[Erro 'Tam. Max']",
            "Tamanho máximo permitido",
            "Elemento",
            "Atributo",
            "não é um valor válido",
            "não é um valor válido",
            "Elemento filho faltando. Era esperado",
            "Falta uma tag no documento",
            "[Erro 'Conteúdo']",
            "um de",
            "falha ao carregar entidade externa",
            "Falha ao tentar localizar o schema principal em",
            "Este elemento não é esperado. Esperado é",
            "não é um dos seguintes possiveis"
        );
        return str_replace($enErr, $ptErr, $msg);
    }
}

<?php

/**
 * Classe auxiliar para o tratamento de strings
 * @category   NFePHP
 * @package    NFePHP\Common\Strings
 * @copyright  Copyright (c) 2008-2017
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux dot rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/nfephp for the canonical source repository
 */

namespace NFePHP\Common;

use NFePHP\Common\Encoding;

class Strings
{
    /**
     * Includes missing or unsupported properties in stdClass inputs
     * and Replace all unsuported chars
     *
     * @param \stdClass $std
     * @param array $possible
     * @return \stdClass
     */
    public static function equilizeParameters(
        \stdClass $std,
        $possible,
        $replaceAccentedChars = false
    ) {
        $arr = get_object_vars($std);
        foreach ($possible as $key) {
            if (!array_key_exists($key, $arr)) {
                $std->$key = null;
            } else {
                if (is_string($std->$key)) {
                    $std->$key = trim(self::replaceUnacceptableCharacters($std->$key));
                    if ($replaceAccentedChars) {
                        $std->$key = self::toASCII($std->$key);
                    }
                }
            }
        }
        return $std;
    }

    /**
     * Replace all specials characters from string and retuns only 128 basics
     * NOTE: only for UTF-8
     * @param string $string
     * @return  string
     */
    public static function replaceSpecialsChars($string)
    {
        $string = self::squashCharacters($string);
        $string = preg_replace("/[^a-zA-Z0-9 @#,-_.;:$%\/]/", "", $string);
        return preg_replace("/[<>]/", "", $string);
    }

    /**
     * Clear inputs for build in XML
     * Only UTF-8 characters is acceptable
     * & isolated, less than, greater than, quotation marks and apostrophes
     * should be replaced by their html equivalent
     * Carriage Return, Tab and Line Feed is not acceptable in strings
     * Multiple spaces is not acceptable in strings
     * And no other control character is acceptable either
     * @param string|null $input
     * @return string|null
     */
    public static function replaceUnacceptableCharacters($input)
    {
        if (empty($input)) {
            return $input;
        }
        if (str_contains($input, '&')) {
            if (
                !(str_contains($input, '&amp;') or
                str_contains($input, '&lt;') or
                str_contains($input, '&gt;') or
                str_contains($input, '&quot;') or
                str_contains($input, '&#39;'))
            ) {
                $input = str_replace('&', ' &amp; ', $input);
            }
        }
        //& isolated, less than, greater than, quotation marks and apostrophes
        //should be replaced by their html equivalent
        $input = str_replace(
            ['<', '>', '"', "'"],
            ['&lt;', '&gt;', '&quot;', '&#39;'],
            $input
        );
        $input = str_replace(['<', '>'], '', $input);
        $input = str_replace("'", '', $input);
        $input = str_replace('"', '', $input);
        $input = preg_replace('/(?:\s\s+)/', ' ', $input);
        $input = self::normalize($input);
        return trim($input);
    }

    /**
     * Converts all UTF-8 remains in ASCII
     * @param string $input
     * @return string
     */
    public static function toASCII($input)
    {
        $input = self::normalize($input);
        $input = self::squashCharacters($input);
        return mb_convert_encoding($input, 'ascii');
    }

    /**
     * Replaces all accented characters of their ASCII equivalents
     * @param string $input
     * @return string
     */
    public static function squashCharacters($input)
    {
        $input = trim($input);
        $aFind = [
            'á', 'à', 'ã', 'â', 'é', 'ê', 'í', 'ó', 'ô', 'õ', 'ö', 'ú', 'ü',
            'ç', 'Á', 'À', 'Ã', 'Â', 'É', 'Ê', 'Í', 'Ó', 'Ô', 'Õ', 'Ö', 'Ú', 'Ü', 'Ç'
        ];
        $aSubs = [
            'a', 'a', 'a', 'a', 'e', 'e', 'i', 'o', 'o', 'o', 'o', 'u', 'u',
            'c', 'A', 'A', 'A', 'A', 'E', 'E', 'I', 'O', 'O', 'O', 'O', 'U', 'U', 'C'
        ];
        return str_replace($aFind, $aSubs, $input);
    }

    /**
     * Replace all non-UTF-8 chars to UTF-8
     * Remove all control chars
     * Remove all multiple spaces
     * @param string $input
     * @return string
     */
    public static function normalize($input)
    {
        //Carriage Return, Tab and Line Feed is not acceptable in strings
        $input = str_replace(["\r", "\t", "\n"], "", $input);
        //Multiple spaces is not acceptable in strings
        $input = preg_replace('/(?:\s\s+)/', ' ', $input);
        //Only UTF-8 characters is acceptable
        $input = Encoding::fixUTF8($input);
        $input = preg_replace(
            '/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
                '|[\x00-\x7F][\x80-\xBF]+' .
                '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
                '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
                '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
            '',
            $input
        );
        $input = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]' .
            '|\xED[\xA0-\xBF][\x80-\xBF]/S', '', $input);
        //And no other control character is acceptable either
        return preg_replace('/[[:cntrl:]]/', '', $input);
    }

    /**
     * Remove all non numeric characters from string
     * @param string $string
     * @return string
     */
    public static function onlyNumbers($string)
    {
        return preg_replace("/[^0-9]/", "", (string) $string);
    }

    /**
     * Remove unwanted attributes, prefixes, sulfixes and other control
     * characters like \r \n \s \t
     * @param string $string
     * @param boolean $removeEncodingTag remove encoding tag from a xml
     * @return string
     */
    public static function clearXmlString($string, $removeEncodingTag = false)
    {
        $aFind = array(
            'xmlns:default="http://www.w3.org/2000/09/xmldsig#"',
            ' standalone="no"',
            'default:',
            ':default',
            "\n",
            "\r",
            "\t"
        );
        $retXml = str_replace($aFind, "", $string);
        $retXml = preg_replace('/(\>)\s*(\<)/m', '$1$2', $retXml);
        if ($removeEncodingTag) {
            $retXml = self::deleteAllBetween($retXml, '<?xml', '?>');
        }
        return $retXml;
    }

    /**
     * Remove all characters between markers
     * @param string $string
     * @param string $beginning
     * @param string $end
     * @return string
     */
    public static function deleteAllBetween($string, $beginning, $end)
    {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }
        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);
        return str_replace($textToDelete, '', $string);
    }

    /**
     * Clears the xml after adding the protocol, removing repeated namespaces
     * @param string $string
     * @return string
     */
    public static function clearProtocoledXML($string)
    {
        $procXML = self::clearXmlString($string);
        $aApp = array('nfe', 'cte', 'mdfe');
        foreach ($aApp as $app) {
            $procXML = str_replace(
                'xmlns="http://www.portalfiscal.inf.br/' . $app . '" xmlns="http://www.w3.org/2000/09/xmldsig#"',
                'xmlns="http://www.portalfiscal.inf.br/' . $app . '"',
                $procXML
            );
        }
        return $procXML;
    }

    /**
     * Remove some alien chars from txt
     * @param string $txt
     * @return string
     */
    public static function removeSomeAlienCharsfromTxt($txt)
    {
        //remove CRs and TABs
        $txt = str_replace(["\r", "\t"], "", $txt);
        //remove multiple spaces
        $txt = preg_replace('/(?:\s\s+)/', ' ', $txt);
        //remove spaces at begin and end of fields
        $txt = str_replace(["| ", " |"], "|", $txt);
        return $txt;
    }

    /**
     * Creates a string ramdomically with the specified length
     * @param int $length
     * @return string
     */
    public static function randomString($length)
    {
        $keyspace = '0123456789abcdefghijklmnopqr'
            . 'stuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[rand(0, $max)];
        }
        return $str;
    }

    /**
     * Converte os caracteres proibidos em XML para entidades, preservando as
     * entidades já existentes na string (numéricas ou nomeadas).
     *
     * - Entidades XML predefinidas (&amp; &lt; &gt; &quot; &apos;) são mantidas.
     * - Entidades numéricas válidas (&#39; &#x27;) são mantidas.
     * - Entidades HTML nomeadas (&aacute; &nbsp; ...) não existem em XML e são
     *   convertidas para a forma numérica.
     * - "&" sem entidade válida, "<", ">", '"' e "'" são convertidos.
     * - Caracteres de controle não permitidos pelo XML 1.0 são removidos, pois
     *   nem como entidade numérica eles são aceitos.
     */
    protected static function xmlEntities(string $text): string
    {
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
        }
        $text = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $text);
        $pattern = '/&(?:#([0-9]+)|#[xX]([0-9a-fA-F]+)|([a-zA-Z][a-zA-Z0-9]*));|[&<>"\']/u';
        return preg_replace_callback($pattern, static function (array $m): string {
            $full = $m[0];
            if (isset($m[1]) && $m[1] !== '') {
                return self::isValidXmlCodePoint((int) $m[1]) ? $full : '&amp;' . substr($full, 1);
            }
            if (isset($m[2]) && $m[2] !== '') {
                return self::isValidXmlCodePoint((int) hexdec($m[2])) ? $full : '&amp;' . substr($full, 1);
            }
            if (isset($m[3]) && $m[3] !== '') {
                if (in_array($m[3], ['amp', 'lt', 'gt', 'quot', 'apos'], true)) {
                    return $full;
                }
                $decoded = html_entity_decode($full, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if ($decoded === $full) {
                    return '&amp;' . substr($full, 1);
                }
                $out = '';
                foreach (mb_str_split($decoded) as $char) {
                    $out .= '&#' . mb_ord($char, 'UTF-8') . ';';
                }
                return $out;
            }
            $map = [
                '&' => '&amp;',
                '<' => '&lt;',
                '>' => '&gt;',
                '"' => '&quot;',
                "'" => '&#39;',
            ];
            return $map[$full];
        }, $text);
    }

    private static function isValidXmlCodePoint(int $cp): bool
    {
        return $cp === 0x9
            || $cp === 0xA
            || $cp === 0xD
            || ($cp >= 0x20 && $cp <= 0xD7FF)
            || ($cp >= 0xE000 && $cp <= 0xFFFD)
            || ($cp >= 0x10000 && $cp <= 0x10FFFF);
    }
}

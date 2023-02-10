<?php

namespace NFePHP\Common;

class Encoding
{
    public const ICONV_TRANSLIT = 'TRANSLIT';
    public const ICONV_IGNORE = 'IGNORE';
    public const WITHOUT_ICONV = 'NOICONV';

    protected static array $win1252ToUtf8 = [
        128 => "\xe2\x82\xac",
        130 => "\xe2\x80\x9a",
        131 => "\xc6\x92",
        132 => "\xe2\x80\x9e",
        133 => "\xe2\x80\xa6",
        134 => "\xe2\x80\xa0",
        135 => "\xe2\x80\xa1",
        136 => "\xcb\x86",
        137 => "\xe2\x80\xb0",
        138 => "\xc5\xa0",
        139 => "\xe2\x80\xb9",
        140 => "\xc5\x92",
        142 => "\xc5\xbd",
        145 => "\xe2\x80\x98",
        146 => "\xe2\x80\x99",
        147 => "\xe2\x80\x9c",
        148 => "\xe2\x80\x9d",
        149 => "\xe2\x80\xa2",
        150 => "\xe2\x80\x93",
        151 => "\xe2\x80\x94",
        152 => "\xcb\x9c",
        153 => "\xe2\x84\xa2",
        154 => "\xc5\xa1",
        155 => "\xe2\x80\xba",
        156 => "\xc5\x93",
        158 => "\xc5\xbe",
        159 => "\xc5\xb8",
    ];

    protected static array $brokenUtf8ToUtf8 = [
        "\xc2\x80" => "\xe2\x82\xac",
        "\xc2\x82" => "\xe2\x80\x9a",
        "\xc2\x83" => "\xc6\x92",
        "\xc2\x84" => "\xe2\x80\x9e",
        "\xc2\x85" => "\xe2\x80\xa6",
        "\xc2\x86" => "\xe2\x80\xa0",
        "\xc2\x87" => "\xe2\x80\xa1",
        "\xc2\x88" => "\xcb\x86",
        "\xc2\x89" => "\xe2\x80\xb0",
        "\xc2\x8a" => "\xc5\xa0",
        "\xc2\x8b" => "\xe2\x80\xb9",
        "\xc2\x8c" => "\xc5\x92",
        "\xc2\x8e" => "\xc5\xbd",
        "\xc2\x91" => "\xe2\x80\x98",
        "\xc2\x92" => "\xe2\x80\x99",
        "\xc2\x93" => "\xe2\x80\x9c",
        "\xc2\x94" => "\xe2\x80\x9d",
        "\xc2\x95" => "\xe2\x80\xa2",
        "\xc2\x96" => "\xe2\x80\x93",
        "\xc2\x97" => "\xe2\x80\x94",
        "\xc2\x98" => "\xcb\x9c",
        "\xc2\x99" => "\xe2\x84\xa2",
        "\xc2\x9a" => "\xc5\xa1",
        "\xc2\x9b" => "\xe2\x80\xba",
        "\xc2\x9c" => "\xc5\x93",
        "\xc2\x9e" => "\xc5\xbe",
        "\xc2\x9f" => "\xc5\xb8",
    ];

    protected static array $utf8ToWin1252 = [
        "\xe2\x82\xac" => "\x80",
        "\xe2\x80\x9a" => "\x82",
        "\xc6\x92" => "\x83",
        "\xe2\x80\x9e" => "\x84",
        "\xe2\x80\xa6" => "\x85",
        "\xe2\x80\xa0" => "\x86",
        "\xe2\x80\xa1" => "\x87",
        "\xcb\x86" => "\x88",
        "\xe2\x80\xb0" => "\x89",
        "\xc5\xa0" => "\x8a",
        "\xe2\x80\xb9" => "\x8b",
        "\xc5\x92" => "\x8c",
        "\xc5\xbd" => "\x8e",
        "\xe2\x80\x98" => "\x91",
        "\xe2\x80\x99" => "\x92",
        "\xe2\x80\x9c" => "\x93",
        "\xe2\x80\x9d" => "\x94",
        "\xe2\x80\xa2" => "\x95",
        "\xe2\x80\x93" => "\x96",
        "\xe2\x80\x94" => "\x97",
        "\xcb\x9c" => "\x98",
        "\xe2\x84\xa2" => "\x99",
        "\xc5\xa1" => "\x9a",
        "\xe2\x80\xba" => "\x9b",
        "\xc5\x93" => "\x9c",
        "\xc5\xbe" => "\x9e",
        "\xc5\xb8" => "\x9f",
    ];

    /**
     * @param string $text
     *
     * @return string
     */
    public static function toUTF8(string $text): string
    {
        $max = self::strlen($text);
        $buf = '';
        for ($i = 0; $i < $max; ++$i) {
            $c1 = $text[$i];
            if ($c1 >= "\xc0") { //Should be converted to UTF8, if it's not UTF8 already
                $c2 = $i + 1 >= $max ? "\x00" : $text[$i + 1];
                $c3 = $i + 2 >= $max ? "\x00" : $text[$i + 2];
                $c4 = $i + 3 >= $max ? "\x00" : $text[$i + 3];
                if ($c1 >= "\xc0" & $c1 <= "\xdf") { //looks like 2 bytes UTF8
                    if ($c2 >= "\x80" && $c2 <= "\xbf") { //yeah, almost sure it's UTF8 already
                        $buf .= $c1 . $c2;
                        ++$i;
                    } else { //not valid UTF8.  Convert it.
                        $cc1 = chr((int)(ord($c1) / 64)) | "\xc0";
                        $cc2 = ($c1 & "\x3f") | "\x80";
                        $buf .= $cc1 . $cc2;
                    }
                } elseif ($c1 >= "\xe0" & $c1 <= "\xef") { //looks like 3 bytes UTF8
                    if (
                        $c2 >= "\x80"
                        && $c2 <= "\xbf"
                        && $c3 >= "\x80"
                        && $c3 <= "\xbf"
                    ) { //yeah, almost sure it's UTF8 already
                        $buf .= $c1 . $c2 . $c3;
                        $i = $i + 2;
                    } else { //not valid UTF8.  Convert it.
                        $cc1 = chr((int)(ord($c1) / 64)) | "\xc0";
                        $cc2 = ($c1 & "\x3f") | "\x80";
                        $buf .= $cc1 . $cc2;
                    }
                } elseif ($c1 >= "\xf0" & $c1 <= "\xf7") { //looks like 4 bytes UTF8
                    if (
                        $c2 >= "\x80"
                        && $c2 <= "\xbf"
                        && $c3 >= "\x80"
                        && $c3 <= "\xbf"
                        && $c4 >= "\x80"
                        && $c4 <= "\xbf"
                    ) { //yeah, almost sure it's UTF8 already
                        $buf .= $c1 . $c2 . $c3 . $c4;
                        $i = $i + 3;
                    } else { //not valid UTF8.  Convert it.
                        $cc1 = chr((int)(ord($c1) / 64)) | "\xc0";
                        $cc2 = ($c1 & "\x3f") | "\x80";
                        $buf .= $cc1 . $cc2;
                    }
                } else { //doesn't look like UTF8, but should be converted
                    $cc1 = chr((int)(ord($c1) / 64)) | "\xc0";
                    $cc2 = ($c1 & "\x3f") | "\x80";
                    $buf .= $cc1 . $cc2;
                }
            } elseif (($c1 & "\xc0") === "\x80") { // needs conversion
                if (isset(self::$win1252ToUtf8[ord($c1)])) { //found in Windows-1252 special cases
                    $buf .= self::$win1252ToUtf8[ord($c1)];
                } else {
                    $cc1 = chr((int)(ord($c1) / 64)) | "\xc0";
                    $cc2 = ($c1 & "\x3f") | "\x80";
                    $buf .= $cc1 . $cc2;
                }
            } else { // it doesn't need conversion
                $buf .= $c1;
            }
        }
        return $buf;
    }

    public static function toWin1252(string $text, ?string $option = self::WITHOUT_ICONV): string
    {
        return self::utf8Decode($text, $option);
    }

    public static function toISO8859(string $text, ?string $option = self::WITHOUT_ICONV): string
    {
        return self::toWin1252($text, $option);
    }

    public static function toLatin1(string $text, ?string $option = self::WITHOUT_ICONV): string
    {
        return self::toWin1252($text, $option);
    }

    public static function fixUTF8(string $text, ?string $option = self::WITHOUT_ICONV): string
    {
        return self::toUTF8(self::utf8Decode($text, $option));
    }

    public static function utf8FixWin1252Chars(string $text): string
    {
        // If you received an UTF-8 string that was converted from Windows-1252 as it was ISO8859-1
        // (ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
        // See: http://en.wikipedia.org/wiki/Windows-1252
        return str_replace(
            array_keys(self::$brokenUtf8ToUtf8),
            array_values(self::$brokenUtf8ToUtf8),
            $text
        );
    }

    public static function removeBOM(?string $str = ''): string
    {
        $bom = pack('CCC', 0xEF, 0xBB, 0xBF);
        if (substr($str, 0, 3) === $bom) {
            $str = substr($str, 3);
        }
        return $str;
    }

    protected static function strlen(string $text): int
    {
        return (int) function_exists('mb_strlen')
            ? mb_strlen($text, '8bit')
            : strlen($text);
    }

    public static function normalizeEncoding(string $encodingLabel): string
    {
        $encoding = strtoupper($encodingLabel);
        $encoding = preg_replace('/[^a-zA-Z0-9\s]/', '', $encoding);
        $equivalences = [
            'ISO88591' => 'ISO-8859-1',
            'ISO8859' => 'ISO-8859-1',
            'ISO' => 'ISO-8859-1',
            'LATIN1' => 'ISO-8859-1',
            'LATIN' => 'ISO-8859-1',
            'UTF8' => 'UTF-8',
            'UTF' => 'UTF-8',
            'WIN1252' => 'ISO-8859-1',
            'WINDOWS1252' => 'ISO-8859-1',
        ];
        if (empty($equivalences[$encoding])) {
            return 'UTF-8';
        }
        return $equivalences[$encoding];
    }

    public static function encode(string $encodingLabel, string $text): string
    {
        $encodingLabel = self::normalizeEncoding($encodingLabel);
        if ('ISO-8859-1' === $encodingLabel) {
            return self::toLatin1($text);
        }
        return self::toUTF8($text);
    }

    protected static function utf8Decode(string $text, ?string $option = self::WITHOUT_ICONV): string
    {
        if (self::WITHOUT_ICONV == $option || !function_exists('iconv')) {
            $str = str_replace(
                array_keys(self::$utf8ToWin1252),
                array_values(self::$utf8ToWin1252),
                self::toUTF8($text)
            );
            $o = mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
        } else {
            $op = self::ICONV_IGNORE === $option ? '//IGNORE' : '';
            $op = self::ICONV_TRANSLIT === $option ? '//TRANSLIT' : $op;
            $o = iconv(
                'UTF-8',
                'Windows-1252' . $op,
                $text
            );
        }
        return $o === false ? '' : $o;
    }
}

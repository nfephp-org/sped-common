# NFePHP\Common\Signer::class

Esta classe é responsável por realizar as assinaturas digitais nos documentos XML e pode ser usado para NFe, NFCe, CTe, MDFe, eSocial, eFinanceira e para os vários modelos de NFSe que requeiram esse tipo de assinatura.


# FORMA DE USO

```php

```

# MÉTODOS

## static function sign(
        Certificate $certificate,
        $content,
        $tagname = '',
        $mark = 'Id',
        $algorithm = OPENSSL_ALGO_SHA1,
        $canonical = [false,false,null,null],
        $rootname = ''
    ):string


## static function removeSignature($xml):string

## static function isSigned($xml, $tagname, $canonical):bool

## public static function existsSignature($xml):bool




# NFePHP\Common\Signer::class

Esta classe é responsável por realizar as assinaturas digitais nos documentos XML e pode ser usado para NFe, NFCe, CTe, MDFe, eSocial, eFinanceira e para os vários modelos de NFSe que requeiram esse tipo de assinatura.


# FORMA DE USO

```php



```

# MÉTODOS

## static function sign(
        Certificate $certificate, //objeto OBRIGATÓRIO 
        $content, //string OBRIGATÓRIA 
        $tagname = '', //string OPCIONAL, em alguns casos
        $mark = 'Id', //string OPCIONAL, em alguns casos
        $algorithm = OPENSSL_ALGO_SHA1, //Opcional em alguns casos
        $canonical = [false,false,null,null], //Opcional em alguns casos
        $rootname = '' //Opcional em alguns casos
    ):string

```php

use NFePHP\Common\Signer;
use NFePHP\Common\Certificate;

$pfx = file_get_contents('certificado_teste.pfx');
$certificate = Certificate::readPfx($pfx, 'associacao');

$xml = "<aqui fica sua string XML que deverá ser assinada>";

$tagname = 'infNFe'; //tag a ser assinada, 
                     //se este campo for deixado vazio a tag raiz será assinada 

$refURI = 'Id'; //indica se a assinatura fará referencia a uma tag 
                //com atributo de identificação definido,
                //se for assinar a raiz do documento este campo deverá 
                //ser deixado em branco 
$root = ''; //este campo indica em qual node a assinatura deverá ser inclusa


$signed = Signer::sign($certificate, $xml, $tagname, $refURI, $root);

```

## static function removeSignature(
        $xml //string OBRIGATÓRIA
    ):string

Este método irá remover a tag &lt;Signature&gt; do XML, caso exista.
Se não existir será retornado o mesmo XML da entrada.

> NOTA: Será retornado um Exception caso a string XML não contenha um XML válido.

```php

use NFePHP\Common\Signer;

$signedxml = "<esta string deve conter o XML já assinado>";

try {
    $unsigned = Signer::removeSignature($signedxml);
} catch (\Exception $e) {
    //aqui você trata a possivel exception
    echo $e->getMessage();
}

```


## static function isSigned(
        $xml,
        $tagname,
        $canonical
    ):bool

Este método verifica a validade da assinatura em um XML.

> NOTA: Limitação apenas nos casos de SHA1 e SHA256, outros possiveis modelos de assinatura não podem ser validados com essa classe.


## public static function existsSignature(
        $xml
    ):bool




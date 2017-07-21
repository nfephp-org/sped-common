# NFePHP\Common\Signer::class

Esta classe é responsável por testar, remover e realizar as assinaturas digitais nos documentos XML e pode ser usado para NFe, NFCe, CTe, MDFe, eSocial, eFinanceira e para os vários modelos de NFSe que requeiram esse tipo de assinatura.

Esta assinatura segue os padrões estabelecidos pelas SEFAZ, Recita Federal e Ministério do Trabalho.

# MÉTODOS

## static function sign():string

Este método realiza a assinatura em um XML. O XML assinado é retornado como string.

> NOTA: essa assinatura está limitada aos padrões da Receita Federal e Sefaz, e permite o uso dos algoritimos SHA1 e SHA256 apenas.

> NOTA: A assinatura será montada mesmo que o certificado estaja vencido !! e com isso gerand uma assinatura INVALIDA.

| Parametro | Tipo | Descrição |
| :---  | :---: | :--- |
| $certificate | Certificate::class | objeto (OBRIGATÓRIO) |
| $content | string | conteudo do XML a ser assinado (OBRIGATÓRIO) | 
| $tagname | string | nome da tag a ser assinada Ex. infNFe (OPCIONAL), em alguns casos |
| $mark    | string | atributo de identificação da tag a ser assinada Ex. Id (OPCIONAL), em alguns casos |
| $algorithm | integer | Ex. OPENSSL_ALGO_SHA1 (Opcional), em alguns casos |
| $canonical | array | opções para obter a forma canonica da string a ser assinada Ex. [true,false,null,null] (Opcional), em alguns casos |
| $rootname  | string | nome da tag que irá conter a assinatura, Ex. '' (Opcional) |

### Exceptions

Este método irá retornar exceptions caso:

- $content vazio
- $content não for um XML válido

**Exemplo de USO**

```php

use NFePHP\Common\Signer;
use NFePHP\Common\Certificate;

$xml = "<aqui fica sua string XML que deverá ser assinada>";

$tagname = 'infNFe'; //tag a ser assinada, 
                     //se este campo for deixado vazio a tag raiz será assinada 

$mark = 'Id'; //indica se a assinatura fará referencia a uma tag 
              //com atributo de identificação definido,
              //se for assinar a raiz do documento este campo deverá 
              //ser deixado em branco 

$algorithm = OPENSSL_ALGO_SHA1; //algoritimo de encriptação a ser usado

$canonical = [true,false,null,null]; //veja função C14n do PHP

$rootname = ''; //este campo indica em qual node a assinatura deverá ser inclusa

try {

    $pfx = file_get_contents('certificado_teste.pfx');
    $certificate = Certificate::readPfx($pfx, 'associacao');

    $signed = Signer::sign(
        $certificate,
        $xml,
        $tagname,
        $mark,
        $algorithm,
        $canonical,
        $rootname
    );
    //$signed contêm o XML assinado
    
    header('Content-type: text/xml; charset=UTF-8');
    echo $signed;

} catch (\Exception $e) {
    //aqui você trata a exceção
    echo $e->getMessage();
}

```

## static function removeSignature():string

Este método irá remover a tag &lt;Signature&gt; do XML, caso exista.
Se não existir será retornado o mesmo XML da entrada.

| Parametro | Tipo | Descrição |
| :---  | :---: | :--- |
| $content  | string | string com o conteúdo de um XML (OBRIGATÓRIA) |

### Exceptions

Este método irá retornar exceptions caso:

- $content não contenha um XML válido.

```php

use NFePHP\Common\Signer;

$content = "<esta string deve conter o XML já assinado>";

try {

    $unsigned = Signer::removeSignature($content);

    header('Content-type: text/xml; charset=UTF-8');
    echo $unsigned;

} catch (\Exception $e) {
    //aqui você trata a possivel exception
    echo $e->getMessage();
}

```


## static function isSigned():bool

Este método verifica a validade de uma assinatura em um XML conforme os padrões da SEFAZ e da Receita Federal.

> NOTA: Existem inumeros possiveis modelos de asssinaturas, mas este método não foi desenhado para avaliar de forma genérica qualquer assinatura. Seu escopo está limitado aos padrões anteriormente indicados.

| Parâmetro | Tipo | Descrição |
| :---  | :---: | :--- |
| $content | string | string com o conteúdo de um XML (OBRIGATÓRIA) |
| $tagname | string | nome da tag a ser usada na validação (OPCIONAL) |
| $canonical | array | opções para obtenção da forma canonica da string a ser assinada (OPCIONAL) |

> NOTA: Limitação apenas nos casos de SHA1 e SHA256, outros possiveis modelos de assinatura não podem ser validados com essa classe.

> NOTA: Caso o XML não esteja assinado irá retornar FALSE

```php

use NFePHP\Common\Signer;

$content = "<esta string deve conter o XML já assinado>";

try {
    if (Signer::isSigned($content)) {
        echo "Assinatura Válida";
    } else {
        echo "Assinatura INVÁLIDA";
    }
} catch (\Exception $e) {
    //aqui você trata a possivel exception
    echo $e->getMessage();
}

```

## static function existsSignature():bool

Este método indica se existe uma assinatura no XML, mas não executa nenhuma outra validação sobre a mesma.

| Parâmetro | Tipo | Descrição |
| :---  | :---: | :--- |
| $content | string | string com o conteúdo de um XML (OBRIGATÓRIA) |


### Exceptions

Este método irá retornar exceptions caso:

- $content não for um XML válido

```php

use NFePHP\Common\Signer;

$content = "<esta string deve conter o XML>";

try {
    if (Signer::existsSignature($content)) {
        echo "O XML contêm uma assinatura.";
    } else {
        echo "O XML NÃO contêm assinatura.";
    }
} catch (\Exception $e) {
    //aqui você trata a possivel exception
    echo $e->getMessage();
}

```

# NFePHP\Common\Validator::class

Esta classe permite a validação de um XML contra seu respectivo XSD

# FORMA DE USO

```php
use NFePHP\Common\Validator;

try {

    $resp = Validator::isValid($xmlcontent, $xsdpath);

} catch (\Exception $e)
    //aqui você trata os exceptions
}

```

$xmlcontent = conteudo do xml que se quer validar

$xsdpath = caminho absoluto até o arquivo xsd

Este método itá retornar TRUE se o XML atender a estrutura estabelecida no XSD ou um EXCEPTION em caso negativo.
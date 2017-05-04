# NFePHP\Common\Certificate\PrivateKey::class

Esta classe irá conter e representar um a CHAVE PRIVADA do certificado digital, ou seja a parte mais IMPORTANTE e restrita desse tipo de documento.

A chave privada é usada entre outras coisas para realizar a assinatura digital de documentos eletrônicos.


# FORMA DE USO

```php
use NFePHP\Common\Certificate\PrivateKey;

//$content = conteúdo da chave privada em formato PEM

$priKey = new PrivateKey($content);

```

# MÉTODOS

## function __construct():this

Método construtor

## function __toString():string

Este método retorna uma strign com a chave privada em formato PEM

```php

echo "{$priKey}";

```

## function sign($content, $algorithm = OPENSSL_ALGO_SHA1):string

Este método cria a assinatura digital usando a chave Privada.

> NOTA: usualmente é usado o algoritimo OPENSSL_ALGO_SHA1, mas existem casos em que poderemos ter que usar outros algoritimos como o OPENSSL_ALGO_SHA256, por exemplo.

> NOTA: uma assinatura é uma string contendo digitos binários obtidos por um algoritimo de segurança que usa as chaves assimetricas do certificado digital. O tamanho da assinutara depende do algoritimo de encriptação utilizado.

```php

$content = "dados a serem assinados";

echo base64_encode($priKey->sign($content, OPENSSL_ALGO_SHA1));
//o retorno foi convertido para base64 pois contêm dados binários

```
# NFePHP\Common\Certificate\PublicKey::class

Esta classe irá conter e representar um certificado digital (ou seja sua CHAVE PÚBLICA).

A chave publica é usada entre outras finalidades para validar uma assinatura digial.

# FORMA DE USO

```php
use NFePHP\Common\Certificate\PublicKey;

//$content = conteúdo da chave publica em uma string

//carregando a classe pelo método direto
$pubKey = new PublicKey($content);

//ou usando o método estatico
$pubKey = PublicKey::createFromContent($content);


```

# MÉTODOS

## function __construct($publicKey):this

Método construtor, recebe como parâmetro a chave publica (certificado) em formato PEM

Vide FORMA DE USO


## function __toString():string

Este método retorna a chave publica como uma string

```php
```

## static function createFromContent($content):PublicKey::class 

Este método instância de forma estatica a classe

```php

echo "{$pubKey}";
```

## function isExpired():bool

Este método retorna TRUE caso o certificado esteja expirado ou FALSE caso ainda seja válido.

```php
if ($pubKey->isExpired()) {
    echo "Certificado INVÁLIDO! Validade Expirada";
} else {
    echo "Válido";
}
```

## function unFormated():string

Este método retorna a chave publica sem as quebras de linhas e sem os marcadores "BEGIN" e "END", ou seja apenas a chave propriamente dita em formto PEM, para poder ser inclusa nos XML assinados ou para outros propositos.

Normalmente um certificado em formato PEM é desta forma (foi omitida boa parte do certificado para fins de apresentação): 

-----BEGIN CERTIFICATE-----
MIIGoTCCBImgAwIBAgIBATANBgkqhkiG9w0BAQ0FADCBlzELMAkGA1UEBhMCQlIx
EzARBgNVBAoTCklDUC1CcmFzaWwxPTA7BgNVBAsTNEluc3RpdHV0byBOYWNpb25h
bCBkZSBUZWNub2xvZ2lhIGRhIEluZm9ybWFjYW8gLSBJVEkxNDAyBgNVBAMTK0F1
DQEBAQUAA4ICDwAwggIKAoICAQC6RqQO3edA8rWgfFKVV0X8bYTzhgHJhQOtmKvS
. . . . 
v+Q68wQ2UCzt3h7bhegdhAnu86aDM1tvR3lPSLX8uCYTq6qz9GER+0Vn8x0+bv4q
qaVtJ8z2KqLRX4Vv4EadqtKlTlUO
-----END CERTIFICATE-----

E sem sua formatação fica assim (foi omitida boa parte do certificado para fins de apresentação): 

MIIGoTCCBImgAwIBAgIBATANBgkqhkiG9w0BAQ0FADCBlzELMAkGA1UEBhMCQlIx ....


```php

$unformatedCert = $pubKey->unFormated();

```

## function verify($data, $signature, $algorithm = OPENSSL_ALGO_SHA1):bool

Este método verifica a validade de uma assinatura 

```php

$data = "dados a serem assinados";
$signature = "rleddaKS731zeLAFuhpXOglVm2UOlAbWxZNvZbNS5NueumeGBSCmxuuYcubUCTgoB+RJzPIzU45eUbfN8B41q+WPWmsyQcWslm7geTyCrWnCJNaYGq5cVJ5eCqTRErQYSo/pBVizDLqyn+UmGUxhn+73sVlPM0kFqiFPpRCmG3azxRD60X48PDi42wvtxbe47FGZuj0XeRqoUvEra2FZPDxoYYrZqvRVHxzZtRpi+Wvp3FcbF+0WsxNgg9xXi4+TgfGDbrOlbx0PxhrvZAWvkKZTiSBKxqvYgeXgIk9KNLkm0UG/u8Gk5DLVEuC3QIdsVcl+dFPapXf0JJIAa4OpjQ==";
//a assinatura foi convertida para base64 pois contêm caracteres binários
if ($pubKey->verify($data, base64_decode($signature), OPENSSL_ALGO_SHA1)) {
    echo "Assinatura Confere !!!";
} else {
    echo "ERRO. A assinatura NÃO confere";
}

```

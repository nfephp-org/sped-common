# NFePHP\Common\Certificate\Asn1::class

A notação de sintaxe abstrata Um (ASN.1) é uma linguagem de descrição de interface para definir estruturas de dados que podem ser serializadas e desserializadas de uma forma padrão e multiplataforma. É amplamente utilizado em telecomunicações e redes de computadores, e especialmente em criptografia.

Os desenvolvedores de protocolo definem estruturas de dados em módulos ASN.1, que geralmente são uma seção de um documento de padrões mais amplo escrito na linguagem ASN.1. Como a linguagem é legível por humanos e legível por máquina, os módulos podem ser transformados automaticamente em bibliotecas que processam suas estruturas de dados, usando um compilador ASN.1.

Esse padão é utilizado na construção dos Certificados Digitais, sejam do tipo A1 (arquivo) ou A3 (tokens).

Esta classe é necessária para poder extrair o numero do CNPJ dos certificados usados no projeto SPED, tendo em vista que essa informação não faz parte do bloco comum de informações de um certificado mundo afora.

Conforme indicado e estabelecido pelo [OID Repository](http://www.oid-info.com/cgi-bin/display?oid=2.16.76.1.3.3&action=display) o identificador que representa o NNPJ é **OID = 2.16.76.1.3.3**.

# FORMA DE USO

```php
use NFePHP\Common\Certificate\Asn1;
use NFePHP\Common\Certificate;

$certificate = Certificate::readPfx($content, $senha);
$cnpj = Asn1::getCNPJ($certificate->publicKey->unFormated());

echo $cnpj;
```

Onde:

$content = conteudo do certificado PFX
$senha = senha do certificado PFX
$cnpj = CNPJ extraido do OID do certificado


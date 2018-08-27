# NFePHP\Common\Certificate\CertificationChain::class

Um certificado digital, como o eNFe ou o eCNPJ, é emitido pelas chamadas "Certificadoras" que usam seus Certificados para essa emissão, ou seja para emitir um certificaod é necessário outro de maior nivel.

As "certificadoras" por sua vez recebem seus certificados de uma ["Autoridade Certificadora"](http://www.iti.gov.br/icp-brasil/certificados), que no Brasil é o ICP que é nossa Autoridade Certificadora Raiz (AC-Raiz), ou seja todos os certificados de uso emprasarial ou pessoal derivam dessa estrutura. 

Durante o processo de autenticação (handshake) em um acesso HTTPS com as unidades da SEFAZ, as vezes o Administrador do serviço do Webservice da SEFAZ, parametriza o mesmo para exigir que o certificado usado na comunicação contenha a cadeia completa de certificação.
Isso não é uma coisa necessária nem mesmo usual, é um ERRO mas não tem com quem discutir!! 
Até o momento a única SEFAZ que já fez essa bobagem é a SEFAZ-GO em seu ambiente de testes, no ambiente de produçã tudo funciona como deveria.

Então a solução é fazer essa inclusão, pois normalmente os certificados da cadeia (usualmente são dois ou três) não estão presentes no arquivo fornecido pelas Certificadores.
O trabalho de "OBTER", e formatar esses certificados é do usuário, nem sempre é uma tarefa muito simples, pois requer um bom conhecimento desse tipo de coisa. 

# SIMULAÇÃO

> NOTA: Vamos fazer uma simulação para efeito demonstrativo, mas lembre-se que existem variações.

## PROBLEMA: Obter a cadeia de certificação de um certificado NFe A1 emitido pela Certisign em janeiro de 2017.

## ETAPA 1 : Obter os certificados da cadeia da Certisign

Para isso iremos até o [site da Certisign](https://www.certisign.com.br/atendimento-suporte/downloads/hierarquias/icp-brasil/nf-e) para baixar os certificados necessários. 

> NOTA: atente ao detalhe que as cadeias podem mudar dependendo da data de emissão e do tipo de certificado, se tiver duvidas, qual cadeia pegar, você deve ver os dados do certificado usando o browser, lá estão indicadas as referencias dos certificados da cadeia.

No nosso caso é um certificado da Hierarquia V2 e V5, emitido depois de 01/09/2016 então a cadeia a usar é:

- AC Raiz V5
- AC Certisign G7
- AC Certisign Multipla G7

Neste caso foram baixados os tres certificados:

- ACRaizV5.cer
- ACCertisignG7.cer
- ACCertisignMultiplaG7.cer

> NOTA: ATENÇÃO, ora os certificados são fornecidos em formato CER, ora em PEM, ora em DER e compete a VOCÊ identificar isso e ver se o formato é aceito pela classe.

Neste caso em particular os certificados foram fornecidos em formato CER, então não é necessário fazer nenhuma conversão.
A classe aceita os certificados nos formatos PEM, DER, CRT ou CER.


# FORMA DE USO

```php

use NFePHP\Common\Certificate\CertificationChain;

$chain1 = file_get_contents('ACCertisignG7.cer');
$chain2 = file_get_contents('ACCertisignMultiplaG7.cer');
$chain3 = file_get_contents('ACRaizV5.cer');

$chain = new CertificationChain();

$chain->add($chain1);
$chain->add($chain2);
$chain->add($chain3);

```

# Métodos 

## function __construct($chainstring):this

Método construtor, pode receber ou não uma string com os certificados da cadeia em formato PEM

## function __toString()

## function add($content):array

Método de adição de certificados, deve receber os certificados (DER, PEM, CER, ou CRT) em uam string. Não pode ser passado um path! 

## function removeExpiredCertificates():void

Método para verificar a validade dos certificados e remover aqueles que já venceram.

```php

$chain->removeExpiredCertificates();

```

## function listChain():array

Método retorna um array PublicKey::class em cada elemento, contendo um certificado da cadeia.

```php
$list = $chain->listChain();

//$list irá conter 
// $list = [Public:class, Public:class, ..., Public:class]

```

## function getExtraCertsForPFX():string

Método que retorna os certificados da cadeia em um array que pode ser usado para adicioná-los em um arquivo PFX juntamente com a chave publica e privada.
Ou seja pode ser usado para recriar o PFX com o conteúdo completo.

```php

$strchain = $chain->getExtraCertsForPFX();

//a variável $strchain conterá uma string com os certificados
//esta função é usada pela classe Certificate::class no método writePfx()

```
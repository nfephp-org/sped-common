# NFePHP\Common\Soap\SoapCurl::class

Esta classe é responsável por realizar a comunicação com os webservices usnado o padrão SOAP da Receita Federal para o projeto SPED.


# FORMA DE USO

```php
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;

$cert = Certificate::readPfx($contentpfx, $password);

$soap = new SoapCurl($cert);


```

# MÉTODOS 

## function __construct(Certificate $certificate = null, LoggerInterface $logger = null)

Método construtor apenas extende o construtor de SoapBase::class

## public function send(
        $url,
        $operation = '',
        $action = '',
        $soapver = SOAP_1_2,
        $parameters = [],
        $namespaces = [],
        $request = '',
        $soapheader = null
    )

Este método é responsável por realizar o envio da solicitação soap ao webservice.

## Métodos SoapBase::class

## function __destruct():void

## function disableSecurity($flag = false):bool

Este método desabilita todas as verificações de segurança da comunicação SOAP. Isso causa inumeras vulnerabilidades permitindo por exemplos ataques do tipo "Man in the meedle". 
Por outro lado se a segurança de comunicação estiver ativa e o certificado da SEFAZ não for reconhecido, poderá haver uma EXCEPTION por falha SSL na comunicação.


## loadCA($capath):string

Este método permite definir a localização do CA a ser usado na sessão SOAP.
Isso é usado para permitir que o cURL reconheça e valide os certificados dos webservices, por exemplo.

> NOTA: normalmente em uma instalação bem feita o php.ini foi parametrizado para usar o CA info mantido pelo sistema, como no exemplo para sistemas DEBIAN like abaixo.
> ```
> [curl]
> ; A default value for the CURLOPT_CAINFO option. This is required to be an
> ; absolute path.
> curl.cainfo = /etc/ssl/certs
> ```

Quando isso não é suficiente, ou não é desejável podemos apontar para um arquivo que contenha os certificados que desejamos reconhecer na comunicação. 

$capath = caminho completo e real até o aquivo que contêm os certificados raiz (ex. cainfo) 

```php
$soap->loadCA($capath)

```
> NOTA: Esse arquivo cainfo pode ser criado usando um sript bash disponivel em sped-nfe.


## function setTemporaryFolder($folderRealPath):void

Na operação com autenticação com certificados digitais o cURL exige que o certificado esteja em um arquivo de disco acessivel diretamente por um "path".
Dito isso a questão é saber "ONDE" podemos colocar de forma temporária esses arquivos "importantes".
Caso não seja especificado um local, atravez desta função, a classe irá salva-los na pasta dos arquivos "temporários" do sistema operacional.

```php

$soap->setTemporaryFolder('/secreto/');

```
> NOTA: lembre-se que o usuário do PHP, do Apache ou do Nginx deve ter permissões de escrita nesta pasta.

## function setDebugMode($value = false):bool

Este método permite ativar ou desativer o modo de debug. Enquanto estiver fazendo testes é interessante o uso do mode de debug pois ele irá salvar tanto as informações de envio como as informações de retorno do webservice, no "temporay Folder" definido. Isso permite que essas informações possan ser analisadas e isso ajuda a solucionar possiveis problemas. 

```php

$soap->setDebugMode(true);

```

## function loadCertificate(Certificate $certificate):void

Se a classe soap foi instanciada sem a passagem dos parametros, a classe Certificate::class pode ser passada posteriormente através deste método.

```php

use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;

$soap = new SoapCurl();

$cert = Certificate::readPfx($contentpfx, $password);

$soap->loadCertificate($cert);

```

## function loadLogger(LoggerInterface $logger):void

Este método permite a inclução de uma classe para manter um LOG como a MONOLOG, desde que atendam ao PSR-3

> **ATENÇÃO: o logger ainda não está funcional!!**

```php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));

$soap->loadLogger($log)

```

## function timeout($timesecs):integer

Este método altera o tempo de timeout que o cURL aguarda para gerar um timeout.

> NOTA: O timeout default é de 20 segundos, muito mais que suficiente para as operações com a SEFAZ

```php

$soap->timeout(30);

```

## function protocol($protocol = self::SSL_DEFAULT):integer

Transport Layer Security (TLS) e seu antecessor, Secure Sockets Layer (SSL), ambos freqüentemente referidos como "SSL", são protocolos criptográficos que fornecem segurança de comunicações através de uma rede de computadores. Várias versões dos protocolos encontram uso generalizado em aplicativos como navegação na web, e-mail, fax via Internet, mensagens instantâneas e voz sobre IP (VoIP). Os sites usam TLS/SSL para proteger todas as comunicações entre seus servidores, navegadores e outros serviços da web.

Especificamente no caso da SEFAZ, hoje a grande maioria das autorizadoras usam TLSv1 (TLSv1.1 ou TLSv1.2), mas a pertir do novo layout 4.0 TODOS usarão apenas TLS e o SSL não mais poderá ser usado.

O PHP geralmente identifica sozinho, durante o handshake com o servidor, qual protocolo ele deve utilizar naquele canal, mas as vezes isso flaha (compilações ruins, sistema ssem atualização e por ai vai).
Nestes casos, em que o PHP não reconhece o protocolo correto, ele terá de ser setado diretamente.

```php

$soap->protocol($soap::SSL_TLSV1_2);

```

## function setSoapPrefix($prefixes):void

Este método não é usado no projeto SPED

```php
```

## function proxy($ip, $port, $user, $password):void

Este método permite a configuração para uso de proxy na rede interna.

```php

$soap->proxy('192.168.0.1', '3128', 'fulano', '1234');

```

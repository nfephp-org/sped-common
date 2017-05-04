# NFePHP\Common\Certificate::class

Esta classe é responsável por tratar a utilizar os certificados digitais modelo A1 (PKCS#12).

# DEPENDÊNCIAS

[NFePHP\Common\Certificate\PrivateKey](Certificate\PrivateKey.md)
[NFePHP\Common\Certificate\PublicKey](Certificate\PublicKey.md)
[NFePHP\Common\Certificate\CertificationChain](Certificate\CertificationChain.md)
[NFePHP\Common\Certificate\Asn1](Certificate\Asn1.md)
[NFePHP\Common\Certificate\SignatureInterface](Certificate\SignatureInterface.md)
[NFePHP\Common\Certificate\VerificationInterface](Certificate\VerificationInterface.md)
[NFePHP\Common\Exception\CertificateException](Exception\CertificateException.md)

# PROPRIEDADES

## public $privateKey
Instância de [PrivateKey::class](Certificate/PrivateKey.md)

## public $publicKey;
Instância de [PublicKey::class](Certificate/PublicKey.md)

## public $chainKeys;
Instância de [CertificationChain::class](Certificate/CertificationChain.md)

# MÉTODOS

## public function __construct(PrivateKey $privateKey, PublicKey $publicKey, CertificationChain $chainKeys = null)::this

- $privateKey = Instância de [PrivateKey::class](Certificate/PrivateKey.md)
- $publicKey = Instância de [PublicKey::class](Certificate/PublicKey.md)
- $chainKeys = Instância de [CertificationChain::class](Certificate/CertificationChain.md)

## public static function readPfx($content, $password)::this

Alternativamente essa classe pode ser carregada estaticamente com a chamada readPfx(), onde:

$content = conteudo do arquivo PFX
$password = senha de acesso ao certificado

NOTA: caso ocorra algum erro será disparada uma EXCEPTION

## public function writePfx($password)::string

Esse método permite que o PFX seja recriado com base em sua chave publica, privada e irá incluir toda a cadeia de certificação, se fornecida.

## public function getCompanyName()::string

Método irá retorna a Razão Social gravada no certificado

## public function getValidFrom()::\DateTime

Método irá retornar uma classe \DateTime com a a data de inicio da validade, ou seja a data de criação do certificado.

## public function getValidTo()::\DateTime

Método irá retornar uma classe \DateTime com a a data de FIM da validade, ou seja a data limite de uso, em geral um ano após a emissão.

## public function isExpired()::bool

Método irá retornar TRUE, se o certificado tiver a data expirada ou seja não está mais válido
ou FALSE se ainda estiver válido.

## public function getCnpj()::string

Método irá retornar o numero do CNPJ

## public function sign($content, $algorithm = OPENSSL_ALGO_SHA1)::string

Este método cria a assinatura digital usando a chave Privada.

## public function verify($data, $signature, $algorithm = OPENSSL_ALGO_SHA1)

Este método valida a assinatura usando a chave Publica.

## public function __toString()::string

Este método retorna a chave publica e a cadeia de certificação, se houver em uma string no formato PEM 

# USO

```php
use NFePHP\Common\Certificate;
use NFePHP\Common\Certificate\CertificationChain;

$strchain = file_get_contents('<PATH TO CHAIN IN PEM FORMAT>');
$chain = new CertificationChain($strchain);

$pfx = file_get_contents('<PATH TO PFX FILE>');
$cert = Certificate::readPfx($pfx, '<PASSWORD>');

```
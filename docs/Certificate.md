# NFePHP\Common\Certificate::class

Esta classe é responsável por tratar a utilizar os certificados digitais modelo A1 (PKCS#12).

## DEPENDÊNCIAS

[NFePHP\Common\Certificate\PrivateKey](Certificate\PrivateKey.md)
[NFePHP\Common\Certificate\PublicKey](Certificate\PublicKey.md)
[NFePHP\Common\Certificate\CertificationChain](Certificate\CertificationChain.md)
[NFePHP\Common\Certificate\Asn1](Certificate\Asn1.md)
NFePHP\Common\Certificate\SignatureInterface
NFePHP\Common\Certificate\VerificationInterface
NFePHP\Common\Exception\CertificateException

## PPROPRIEDADES

### public $privateKey
Instância de PrivateKey::class

### public $publicKey;
Instância de PublicKey::class

### public $chainKeys;
Instância de CertificationChain::class

## MÉTODOS

### public static function readPfx($content, $password)

### public function writePfx($password)

### public function getCompanyName()

### public function getValidFrom()

### public function getValidTo()

### public function isExpired()

### public function getCnpj()

### public function sign($content, $algorithm = OPENSSL_ALGO_SHA1)

### public function verify($data, $signature, $algorithm = OPENSSL_ALGO_SHA1)

### public function __toString()


## USO

```php
use NFePHP\Common\Certificate;
use NFePHP\Common\Certificate\CertificationChain;

$pfx = file_get_contents('<PATH TO PFX FILE>');
//$strchain = file_get_contents('<PATH TO CHAIN IN PEM FORMAT>');
//$chain = new CertificationChain($strchain);
$cert = Certificate::readPfx($pfx, '<PASSWORD>');
```
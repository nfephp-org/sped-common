# NFePHP\Common\Keys.md

Classe auxiliar para a geração e verficação das chaves de 44 digitos

> NOTA: Pode ser usado para NFe, NFCe, CTe, e MDFe

> NOTA: Inclusa possibilidade de emissão com CPF no lugar do CNPJ,
> conforme NT 2018.001

## Métodos Estáticos


### (string) Keys::build(
    (string) $cUF,
    (string) $ano,
    (string) $mes,
    (string) $cnpj ou cpf,
    (string) $mod,
    (string) $serie,
    (string) $numero,
    (string) $tpEmis,
    (string) $codigo
    )

Este método é o construtor das chaves de 44 digitos.

```php

use NFePHP\Common\Keys;

$cUF = '35';
$ano = '17';
$mes = '4';
$cnpj = '58716523000119';
$mod = '55';
$serie = '1';
$numero = '12';
$tpEmis = '1';
$codigo = '12345';

$key = Keys::build($cUF, $ano, $mes, $cnpj, $mod, $serie, $numero, $tpEmis, $codigo);

//$key = '35170458716523000119550010000000121000123458';

```
> NOTA: se não for passado o codigo numero ($codigo) será usado o numero do documento fiscal ($numero).
> Isso as vezes é até melhor pois facilita a recriação da chave caso haja algum problema e se percam os dados das notas.



### (bool) Keys::isValid(string $key)

Este mátodo irá verificar a validade da chave de 44 digitos, usando o digito de verificação para fazer a avaliação.

```php

use NFePHP\Common\Keys;

$key = '35170358716523000119550010000000301000000300';

$response = Keys::isValid($key); 

//$response = true
```
 

### (string) Keys::verifyingDigit(string $key)

Este método irá retornar o digito de verificação da chave passada.

> NOTA: apenas os 43 primeiros digitos serão usados, evidentemente.

```php

use NFePHP\Common\Keys;

$key = '3517035871652300011955001000000030100000030';

$digit = Keys::verifyingDigit($key);

//digito retornado = 0
```

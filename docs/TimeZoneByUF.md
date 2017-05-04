# NFePHP\Common\TimeZoneByUF::class

Como o Brasil tem 4 diferentes zonas de tempo, e as datas inclusas nos xml do projeto SPED requerem sua indicação correta, torna-se muito importante esse gerenciamento para evitar rejeições devido a incorreções na data/hora indicada pela API.

> NOTA: lembre-se que nos campos de data a SEFAZ incluiu o TZD (ex. 2017-01-04T12:34:01-03.00)


# FORMA DE USO

```php

use NFePHP\Common\TimeZoneByUF;

try {

    //recupera o TZD pela sigla
    $tzd = TimeZoneByUF::get('SP');
    //$tzd = America/Sao_Paulo

    //ou
    //recupera o TZD pela codigo do estado
    $tzd = TimeZoneByUF::get(35);
    //$tzd = America/Sao_Paulo

} catch (\Exception $e) {
    //aqui você trata os erros
}

```
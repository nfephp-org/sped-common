# NFePHP\Common\DOMImproved::class

Extende a classe DOMDocument

# MÉTODOS

## function getNodeValue($nodeName, $itemNum = 0, $extraTextBefore = '', $extraTextAfter = ''):string

## function getValue(DOMElement $node, $name):string

## function getNode($nodeName, $itemNum = 0):DOMElement

## function getChave($nodeName = 'infNFe'):string

## function addChild(
        DOMElement &$parent,
        $name,
        $content,
        $obrigatorio = false,
        $descricao = '',
        $force = false
    ):void

| Parametro | Tipo | Descrição |
| :--- | :---: | :--- |
| $parent | DOMElement | Tag PAI |
| $name | string | nome da tag filha |
| $content | null, string, float, int | conteudo a ser inserido na tag filha |
| $obrigatorio | boolean | se true a tag será inserida mesmo vazia |
| $descricao | string | auxilio para tratamento de erro |
| $force | boolean | se true a tag será inserida mesmo vazia |

NOTA: se $content for NULL nada será feito, a operação será ignorada.
 


## function appChild(DOMElement &$parent, DOMElement $child = null, $msg = ''):void


## function appExternalChild(DOMElement &$parent, DOMElement $child):void

## function appExternalChildBefore(
        DOMElement &$parent,
        DOMElement $child,
        $before
    ):void

## function appChildBefore(DOMElement &$parent, DOMElement $child = null, $before = '', $msg = ''):void


## function addArrayChild(DOMElement &$parent, $arr):integer
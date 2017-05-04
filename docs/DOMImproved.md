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
        $content = '',
        $obrigatorio = false,
        $descricao = '',
        $force = false
    ):void

## function appChild(DOMElement &$parent, DOMElement $child = null, $msg = ''):void


## function appExternalChild(DOMElement &$parent, DOMElement $child):void

## function appExternalChildBefore(
        DOMElement &$parent,
        DOMElement $child,
        $before
    ):void

## function appChildBefore(DOMElement &$parent, DOMElement $child = null, $before = '', $msg = ''):void


## function addArrayChild(DOMElement &$parent, $arr):integer
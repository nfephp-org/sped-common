<?php

namespace NFePHP\Common;

/**
 * Extends DOMDocument
 * @category   NFePHP
 * @package    NFePHP\Common\DOMImproved
 * @copyright  Copyright (c) 2008-2016
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Roberto L. Machado <linux.rlm at gmail dot com>
 * @link       http://github.com/nfephp-org/sped-common for the canonical source repository
 */

use DOMDocument;
use DOMElement;

class DOMImproved extends DOMDocument
{
    /**
     * @var array
     */
    public $error = [];
    
    /**
     * @param string $version
     * @param string $charset
     */
    public function __construct($version = '1.0', $charset = 'utf-8')
    {
        parent::__construct($version, $charset);
        $this->formatOutput = false;
        $this->preserveWhiteSpace = false;
    }
    
    /**
     * Loads string in DOMDocument
     * @param string $content content of xml
     * @return bool
     */
    public function loadXMLString($content)
    {
        $msg = "O arquivo indicado não é um XML ou contêm B.O.M. no inicio do arquivo !";
        if (substr($content, 0, 1) != '<' ||
            !$this->loadXML($content, LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG)
        ) {
            $this->error[] = $msg;
            return false;
        }
        return true;
    }
    
    /**
     * Load xml from path
     * @param string $filename
     * @return bool
     */
    public function loadXMLFile($filename)
    {
        if (!is_file($filename)) {
            $this->error[] = 'Arquivo não encontrado!';
            return false;
        }
        $content = file_get_contents($filename);
        $this->loadXMLString($content);
    }
            
    /**
     * Extrai o valor do node DOM
     * @param string $nodeName identificador da TAG do xml
     * @param int $itemNum numero do item a ser retornado
     * @param string $extraTextBefore prefixo do retorno
     * @param string $extraTextAfter sufixo do retorno
     * @return string
     */
    public function getNodeValue($nodeName, $itemNum = 0, $extraTextBefore = '', $extraTextAfter = '')
    {
        $node = $this->getElementsByTagName($nodeName)->item($itemNum);
        if (isset($node)) {
            $texto = html_entity_decode(trim($node->nodeValue), ENT_QUOTES, 'UTF-8');
            return $extraTextBefore . $texto . $extraTextAfter;
        }
        return '';
    }
    
    /**
     * getValue
     * @param DOMElement $node
     * @param string $name
     * @return string
     */
    public function getValue(DOMElement $node, $name)
    {
        if (empty($node)) {
            return '';
        }
        $texto = !empty($node->getElementsByTagName($name)->item(0)->nodeValue) ?
            $node->getElementsByTagName($name)->item(0)->nodeValue : '';
        return html_entity_decode($texto, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * getNode
     * Retorna o node solicitado
     * @param string $nodeName
     * @param integer $itemNum
     * @return DOMElement se existir ou string vazia se não
     */
    public function getNode($nodeName, $itemNum = 0)
    {
        $node = $this->getElementsByTagName($nodeName)->item($itemNum);
        if (isset($node)) {
            return $node;
        }
        return '';
    }
    
    /**
     * getChave
     * @param string $nodeName
     * @return string
     */
    public function getChave($nodeName = 'infNFe')
    {
        $node = $this->getElementsByTagName($nodeName)->item(0);
        if (! empty($node)) {
            $chaveId = $node->getAttribute("Id");
            $chave =  preg_replace('/[^0-9]/', '', $chaveId);
            return $chave;
        }
        return '';
    }
    
    /**
     * addChild
     * Adiciona um elemento ao node xml passado como referencia
     * Serão inclusos erros na array $erros[] sempre que a tag for obrigatória e
     * nenhum parâmetro for passado na variável $content e $force for false
     * @param DOMElement $parent
     * @param string $name
     * @param string $content
     * @param boolean $obrigatorio
     * @param string $descricao
     * @param boolean $force força a criação do elemento mesmo sem dados e não considera como erro
     * @return void
     */
    public function addChild(
        DOMElement &$parent,
        $name,
        $content = '',
        $obrigatorio = false,
        $descricao = '',
        $force = false
    ) {
        $content = trim($content);
        if ($obrigatorio && $content === '' && !$force) {
            $this->erros[] = "Preenchimento Obrigatório! [$name] $descricao";
        }
        if ($obrigatorio || $content !== '' || $force) {
            $content = htmlspecialchars($content, ENT_QUOTES);
            $temp = $this->createElement($name, $content);
            $parent->appendChild($temp);
        }
    }
    
    /**
     * appChild
     * Acrescenta DOMElement a pai DOMElement
     * Caso o pai esteja vazio retorna uma exception com a mensagem
     * O parametro "child" pode ser vazio
     * @param DOMElement $parent
     * @param DOMElement $child
     * @param string $msg
     * @return void
     */
    public function appChild(DOMElement &$parent, DOMElement $child, $msg = '')
    {
        if (empty($parent) || empty($child)) {
            $this->erros[] = $msg;
            return;
        }
        $parent->appendChild($child);
    }
    
    /**
     * appChildBefore
     * Acrescenta DOMElement a pai DOMElement
     * Caso o pai esteja vazio retorna uma exception com a mensagem
     * O parametro "child" pode ser vazio
     * @param DOMElement $parent
     * @param DOMElement $child
     * @param string $before
     * @param string $msg
     * @return void
     */
    public function appChildBefore(DOMElement &$parent, DOMElement $child, $before, $msg = '')
    {
        if (empty($parent) ||
            empty($child) ||
            empty($bnode = $parent->getElementsByTagName($before)->item(0))
        ) {
            $this->error[] = "Node parent ou node child vazios ou node <$before> não encontrado!!";
            return;
        }
        $parent->insertBefore($child, $bnode);
    }
    
    /**
     * addArrayChild
     * Adiciona a um DOMElemt parent, outros elementos passados em um array de DOMElements
     * @param DOMElement $parent
     * @param array $arr
     * @return int
     */
    public function addArrayChild(DOMElement &$parent, $arr)
    {
        $num = 0;
        if (! empty($arr) && ! empty($parent)) {
            foreach ($arr as $node) {
                $this->appChild($parent, $node, '');
                $num++;
            }
        }
        return $num;
    }
}

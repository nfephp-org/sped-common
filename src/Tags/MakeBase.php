<?php

namespace NFePHP\Common\Tags;

/**
 * Abstract class to build Make::class
 */

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Keys;
use NFePHP\Common\Tags\TagInterface;
use \DOMElement;

abstract class MakeBase
{
    /**
     * @var NFePHP\Common\DOMImproved
     */
    protected $dom;
    /**
     * @var \DOMElement
     */
    protected $root;
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $rootname = '';
    /**
     * @var string
     */
    protected $xmlns = '';
    /**
     * @var bool
     */
    protected $onlyAscii = false;
    /**
     * @var array
     */
    protected $available = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dom = new Dom('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = false;
        $this->root = $this->dom->createElement($this->rootname);
        $this->root->setAttribute("xmlns", $this->xmlns);
    }
    
    /**
     * To force convertion strings to ASCII
     * @param bool $flag
     * @return bool
     */
    public function setToAscii($flag = null)
    {
        if (isset($flag) && is_bool($flag)) {
            $this->onlyAscii = $flag;
        }
        return $this->onlyAscii;
    }
    
    /**
     * Abstract function to convert Tag::classes into DOM objects
     */
    abstract public function parse();

    /**
     * Call classes to build each element for XML
     * @param string $name
     * @param array $arguments [std]
     * @return object|array
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        $name = str_replace('-', '', strtolower($name));
        $realname = $name;
        $arguments[0]->onlyAscii = $this->onlyAscii;
        if (!array_key_exists($realname, $this->available)) {
            throw new \Exception("Não encontrada referencia ao método $name.");
        }
        $className = $this->available[$realname]['class'];
        if (empty($arguments[0])) {
            throw new \Exception("Sem dados passados para o método [$name].");
        }
        $propname = str_replace('tag', '', $name);
        $c = $this->loadTagClass($className, $arguments);
        if ($this->available[$realname]['type'] == 'multiple') {
            if (!isset($this->$propname)) {
                $this->createProperty($propname, []);
            }
            array_push($this->$propname, $c);
        } else {
            $this->createProperty($propname, $c);
        }
        return $this->$propname;
    }
    
    /**
     * Load Tag::class
     * @param string $className
     * @param array $arguments
     * @return \NFePHP\Common\Tags\className
     */
    protected function loadTagClass($className, $arguments)
    {
        $c = new $className($this->dom);
        $c->setToASCII($this->onlyAscii);
        $c->loadParameters($arguments[0]);
        return $c;
    }

    /**
     * Create properties
     * @param string $name
     * @param TagInterface $value
     */
    public function createProperty($name, TagInterface $value)
    {
        $this->{$name} = $value;
    }
    
    /**
     * Calculate and replace document Id
     * @return string
     */
    protected function checkIdKey()
    {
        $doc = isset($this->emit->std->cnpj) ? $this->emit->std->cnpj : $this->emit->std->cpf;
        if (empty($this->ide->std->dhemi)) {
            $this->ide->std->dhemi = (new \DateTime())->format('Y-m-d\TH:i:sP');
        }
        $dt = new \DateTime($this->ide->std->dhemi);
        $buildId = Keys::build(
            $this->ide->std->cuf,
            $dt->format('y'),
            $dt->format('m'),
            $doc,
            $this->ide->std->mod,
            $this->ide->std->serie,
            $this->ide->std->nnf,
            $this->ide->std->tpemis,
            $this->ide->std->cnf
        );
        $infid = str_replace($this->rootname, '', $this->infnfe->std->id);
        if ($buildId != $infId) {
            $this->infnfe->std->id = "{$this->rootname}{$buildId}";
            $this->ide->std->cdv = substr($buildId, -1);
        }
        return $buildId;
    }
}

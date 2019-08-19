<?php

namespace NFePHP\Common\Tags;

use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Strings;
use \DOMElement;
use \stdClass;

abstract class Tag
{
    /**
     * @var DOMElement
     */
    protected $node;
    /**
     * @var NFePHP\Common\DOMImproved
     */
    protected $dom;
    /**
     * @var bool
     */
    protected $onlyAscii = false;
    
    /**
     * Constructor
     * @param NFePHP\Common\DOMImproved $dom
     */
    public function __construct($dom = null)
    {
        $this->dom = $dom;
        if (empty($dom)) {
            $this->dom = new Dom('1.0', 'UTF-8');
            $this->dom->preserveWhiteSpace = false;
            $this->dom->formatOutput = false;
        }
    }
    
    /**
     * Set to convert all string to have only ASCII characters
     * @param bool $option
     */
    public function setToASCII($option = false)
    {
        $this->onlyAscii = $option;
    }
    
    /**
     * Load class parameters
     */
    abstract public function loadParameters(\stdClass $std);


    /**
     * Convert tag classes into nodes
     */
    abstract public function toNode();
    
    /**
     * Retruns node as string
     * @return string
     */
    public function __toString()
    {
        if (empty($this->node)) {
            $this->node = $this->toNode();
        }
        return preg_replace("/<\\?xml.*\\?>/", '', $this->dom->saveXML($this->node));
    }
    
    /**
     * Includes missing or unsupported default properties in stdClass
     * @param stdClass $std  fields
     * @param array $possible  possible fields
     * @return stdClass
     */
    public function equalize($std, $node, $possible)
    {
        $errors = [];
        $arr = array_change_key_case(get_object_vars($std), CASE_LOWER);
        $std = json_decode(json_encode($arr));
        $possibles = array_keys($possible);
        $psstd = json_decode(json_encode($possible));
        $newstd = new \stdClass();
        foreach ($possibles as $key) {
            $possibleKeyLower = strtolower($key);
            if (!key_exists($possibleKeyLower, $arr)) {
                $newstd->$possibleKeyLower = null;
            } else {
                $newstd->$possibleKeyLower = $std->$possibleKeyLower;
            }
            if ($newstd->$possibleKeyLower === null && $possible[$key]['required']) {
                $errors[] = "O campo $node:$key (" . $possible[$key]['info'] . ") é OBRIGATÓRIO.";
            }
            if ($newstd->$possibleKeyLower !== null) {
                if ($err = $this->fieldIsInError($newstd->$possibleKeyLower, $key, $node, $possible[$key])) {
                    $errors[] = $err;
                }
                $newstd->$possibleKeyLower = (string) $newstd->$possibleKeyLower;
                if ($this->onlyAscii) {
                    $newstd->$possibleKeyLower = Strings::replaceSpecialsChars($newstd->$possibleKeyLower);
                } else {
                    $newstd->$possibleKeyLower = Strings::replaceUnacceptableCharacters($newstd->$possibleKeyLower);
                }
                $param = $possible[$key];
                $newstd->$possibleKeyLower = $this->formater($newstd->$possibleKeyLower, $param['format']);
            }
        }
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode("\n", $errors));
        }
        return $newstd;
    }
    
    /**
     * Check if the data given meets the parameters
     * if false is no errors
     * if string the input does not meet the requirements
     * @param string|float|integer $input
     * @param string $fieldname
     * @param string $nodename
     * @param array $pattern
     * @return bool|string
     */
    protected function fieldIsInError($input, $fieldname, $nodename, $pattern)
    {
        $type = $pattern['type'];
        $regex = $pattern['regex'];
        if (empty($regex)) {
            return false;
        }
        switch ($type) {
            case 'integer':
                if (!is_integer($input)) {
                    return "$nodename campo: $fieldname deve ser um valor numérico inteiro.";
                }
                break;
            case 'numeric':
                if (!is_numeric($input)) {
                    return "$nodename campo: $fieldname deve ser um numero.";
                }
                break;
            case 'string':
                if (!is_string($input)) {
                    return "$nodename campo: $fieldname deve ser uma string.";
                }
                break;
            case 'gtin':
                try {
                    if (class_exists('\NFePHP\Gtin\Gtin')) {
                        \NFePHP\Gtin\Gtin::check($input)->isValid();
                    }
                } catch (\Exception $e) {
                    return "$nodename campo: $fieldname deve ser um numero GTIN válido. \n" . $e->getMessage();
                }
                break;
        }
        $input = (string) $input;
        if ($regex === 'email') {
            if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                return "$nodename campo: $fieldname Esse email [$input] está incorreto.";
            }
            return false;
        }
        if (!preg_match("/$regex/", $input)) {
            return "$nodename campo: $fieldname valor incorreto [$input]. (validação: $regex)";
        }
        return false;
    }
   
    /**
     * DOM constructor based in parameters
     * @param \DOMElement $node
     * @param \stdClass $std
     * @param array $parameters
     * @return void
     */
    protected function builder(&$node, $std, $parameters)
    {
        foreach ($parameters as $key => $param) {
            if ($key === 'item') {
                continue;
            }
            $keyLower = strtolower($key);
            $value = (string) $std->$keyLower;
            if ($param['format'] === 'cdata') {
                $ncdata = $node->appendChild($this->dom->createElement($key));
                $ncdata->appendChild($this->dom->createCDATASection($value));
                return;
            }
            if ($param['position'] === 'attribute') {
                if ($param['required'] || $std->$keyLower !== null) {
                    $node->setAttribute(
                        $key,
                        $value
                    );
                }
            }
            if ($param['position'] === 'node') {
                $this->dom->addChild(
                    $node,
                    $key,
                    $value,
                    $param['required'],
                    $param['info']
                );
            }
        }
    }
    
    /**
     * Format float numbers if necessary
     * @param string $value
     * @param string $format
     * @return string
     */
    protected function formater($value, $format = null)
    {
        if (empty($format) || !isset($value)) {
            return $value;
        }
        if (!is_numeric($value)) {
            return trim($value);
        }
        $n = explode('v', $format);
        $mdec = strpos($n[1], '-');
        $p = explode('.', $value);
        
        $ndec = !empty($p[1]) ? strlen($p[1]) : 0;//decimal digits
        $nint = strlen($p[0]);//integer digits
        if ($nint > $n[0]) {
            throw new \InvalidArgumentException("O numero é maior que o permitido [$format].");
        }
        if ($mdec !== false) {
            //is multi decimal
            $mm = explode('-', $n[1]);
            $decmin = $mm[0];
            $decmax = $mm[1];
            //verificar a quantidade de decimais informada
            //se menor que o minimo, formata para o minimo
            if ($ndec <= $decmin) {
                return number_format($value, $decmin, '.', '');
            }
            //se maior que o minimo, formata para o maximo
            if ($ndec > $decmin) {
                return number_format($value, $decmax, '.', '');
            }
        }
        return number_format($value, $n[1], '.', '');
    }
    
    /**
     * Add left zeros if necessary
     * @param string $value
     * @param integer $length
     * @return string
     */
    protected function zeroLeft($value, $length)
    {
        return str_pad($value, $length, '0', STR_PAD_LEFT);
    }
}

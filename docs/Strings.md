# NFePHP\Common\Strings::class

Auxiliar class for strings treatment

# Methods

## replaceSpecialsChars()
Replace all specials characters from string and retuns only 128 basic characters. [a-z] [A-Z] [0-9] @ space , - . ; :

> NOTE: only UTF-8

- @param string $string
- @return  string 

```php
    $str = "Á é ç ";
    $ret = Strings::replaceSpecialsChars($str);
    echo $ret; //A e c
```
## clearXml()
Remove some attributes, prefixes, sulfixes and other control characters like \r \n \s \t

- @param string $xml
- @param boolean $removeEncodingTag default FALSE
- @return string

```php
    $ret = Strings::clearXml($xml);
    echo $ret; //clean xml will returned
```

## clearProtocoledXML()
Clears the xml after adding the protocol, removing repeated namespaces.

- @param string $string
- @return string

```php
    $protxml = Strings::clearProt($xmlstring);
    echo $protxml; //clean xml will returned
```

## deleteAllBetween()
Remove all characters between markers.

- @param string $string
- @param string $beginning
- @param string $end
- @return string

```php
    $str = '<?xml version="1.0" encoding="UTF-8"?><nfeProc versao="3.10" xmlns="http://www.portalfiscal.inf.br/nfe">
    <NFe xmlns="http://www.portalfiscal.inf.br/nfe">';
    
    $ret = Strings::deleteAllBetween($str, '<?xml', '?>');
    echo $ret; //returns xml without encoding tag
```

## randomString()
Creates a string ramdomically with the specified length.

- @param int $length
- @return string

```php
    $randomStr = Strings::randomString(5);
    echo $randomStr; //zRT20
```

## removeSomeAlienCharsfromTxt()
- Removes [CR] and [TAB]
- Removes duplicated [spaces]
- Removes [spaces] after | (pipe)
- Removes [spaces] before | (pipe)

- @param string $txt
- @return string

```php
    $txtclean = Strings::removeSomeAlienCharsfromTxt($txt);
    echo $txtclean; //returns txt without this characters  
```

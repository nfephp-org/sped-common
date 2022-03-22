<?php

namespace NFePHP\Common\Exception;

class InvalidXmlException extends \Exception
{
    const XML_EMPTY = 9001; //vazio
    const XML_INVALID = 9002; //não integro ou não xml
    const XML_NOT_FOUND = 9003; //node não encontrado
}

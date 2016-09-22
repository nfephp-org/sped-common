<?php

namespace NFePHP\Common\Soap;

use SoapClient;

class SoapClientExtended extends SoapClient
{
   /**
     * __construct
     * @param mixed $wsdl NULL for non-wsdl mode or URL string for wsdl mode
     * @param array $options
     */
    public function __construct($wsdl, $options)
    {
        parent::SoapClient($wsdl, $options);
    }
    
    /**
     * __doRequest
     * Changes the original behavior of the class by removing prefixes,
     * suffixes and line breaks that are not supported by some webservices
     * due to their particular settings
     * @param  string $request
     * @param  string$location
     * @param  string $action
     * @param  int $version
     * @param  int $oneWay
     * @return string
     */
    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        $aFind = array(":ns1","ns1:","\n","\r");
        $sReplace = '';
        $newrequest = str_replace($aFind, $sReplace, $request);
        return parent::__doRequest($newrequest, $location, $action, $version, $oneWay);
    }
}

<?php
namespace NFePHP\Common\Certificate\Exception;

use NFePHP\Common\Certificate;
use NFePHP\Common\Exception\ExceptionInterface;
use NFePHP\Common\Exception\RuntimeException;

class Expired extends RuntimeException implements ExceptionInterface
{
    public function __construct(Certificate $certificate)
    {
        $invalidDate = $certificate->getValidTo()->format('Y-m-d H:i:s');
        parent::__construct('Certificate era válido até '.$invalidDate, 0, null);
    }
}

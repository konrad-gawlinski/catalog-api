<?php

namespace Nu3\Service\Product;

class Exception extends \Exception
{
  private $errorKey = '';

  function __construct($errorKey)
  {
    $this->errorKey = $errorKey;
    parent::__construct('Product service error: '. $errorKey);
  }

  function errorKey()
  {
    return $this->errorKey;
  }
}
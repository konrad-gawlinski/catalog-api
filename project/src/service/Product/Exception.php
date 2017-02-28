<?php

namespace Nu3\Service\Product;

use Nu3\Core\Violation;

class Exception extends \Exception
{
  private $errorType = '';

  function __construct($message, $errorType)
  {
    parent::__construct($message);
    $this->errorType = $errorType;
  }

  function getViolation() : Violation
  {
    return new Violation($this->getMessage(), $this->errorType);
  }
}
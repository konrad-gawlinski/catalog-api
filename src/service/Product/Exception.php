<?php

namespace Nu3\Service\Product;

use Nu3\Core\Violation;

class Exception extends \Exception
{
  function __construct($errorKey)
  {
    parent::__construct($errorKey);
  }

  function getViolation() : Violation
  {
    return new Violation($this->getMessage());
  }
}

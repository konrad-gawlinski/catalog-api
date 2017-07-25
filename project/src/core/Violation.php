<?php

namespace Nu3\Core;

class Violation
{
  private $errorKey = '';

  function __construct($errorKey)
  {
    $this->errorKey = $errorKey;
  }

  function errorKey() : string
  {
    return $this->errorKey;
  }
}

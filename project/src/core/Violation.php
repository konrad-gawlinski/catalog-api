<?php

namespace Nu3\Core;

class Violation
{
  private $errorKey = '';
  private $message = '';

  function __construct($errorKey, $message = null)
  {
    $this->errorKey = $errorKey;
    $this->message = $message ?: $errorKey;
  }

  function errorKey() : string
  {
    return $this->errorKey;
  }

  function message() : string
  {
    return $this->message;
  }
}

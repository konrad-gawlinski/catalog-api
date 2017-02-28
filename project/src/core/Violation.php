<?php

namespace Nu3\Core;

class Violation
{
  const ET_REQUEST = 'request_error';
  const ET_DATABASE = 'database_error';

  private $message = '';
  private $errorType = '';

  function __construct($message, $errorType=0)
  {
    $this->message = $message;
    $this->errorType = $errorType;
  }

  function message() : string
  {
    return $this->message;
  }

  function errorType() : string
  {
    return $this->errorType;
  }
}

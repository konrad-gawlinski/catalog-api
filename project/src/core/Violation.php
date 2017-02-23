<?php

namespace Nu3\Core;

class Violation
{

  const EK_REQUEST = 'request_error';
  const EK_DATABASE = 'database_error';

  private $message = '';
  private $errorKey = '';

  function __construct($message, $errorKey=0)
  {
    $this->message = $message;
    $this->errorKey = $errorKey;
  }

  function message() : string
  {
    return $this->message;
  }

  function errorKey() : string
  {
    return $this->errorKey;
  }

}
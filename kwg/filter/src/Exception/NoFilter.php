<?php

namespace Kwg\Filter\Exception;

class NoFilter extends Exception
{
  function __construct()
  {
    parent::__construct('No filter provided');
  }
}
<?php

namespace Nu3\Service\Product\Feature;

use Nu3\Service\Product\Request;

trait RequestId
{
  function getId(): string
  {
    return $this->getValue(Request::PROPERTY_ID);
  }
}

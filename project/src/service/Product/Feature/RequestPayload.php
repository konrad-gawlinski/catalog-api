<?php

namespace Nu3\Service\Product\Feature;

use Nu3\Service\Product\Request;

trait RequestPayload
{
  function getPayload()
  {
    return $this->getValue(Request::PROPERTY_PAYLOAD);
  }
}

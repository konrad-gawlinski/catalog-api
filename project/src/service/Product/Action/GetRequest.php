<?php

namespace Nu3\Service\Product\Action;

use Nu3\Service\Product\Request;

class GetRequest extends Request
{
  const PROPERTY_ID = 'id';

  function getId(): string
  {
    return $this->getValue(self::PROPERTY_ID);
  }
}

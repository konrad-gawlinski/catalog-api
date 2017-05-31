<?php

namespace Nu3\Service\Product\GetAction;

use Nu3\Service\Product\Property;

class Request
{
  private $params = [];

  function __construct(array $params)
  {
    $this->params = $params;
  }

  function sku(): string
  {
    return $this->getValue(Property::PRODUCT_SKU);
  }

  function storage(): string
  {
    return $this->getValue(Property::STORAGE);
  }

  private function getValue($name, $default=''): string
  {
    if (isset($this->params[$name])) return $this->params[$name];

    return $default;
  }
}

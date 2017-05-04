<?php

namespace Nu3\Service\Product\Request;

use Nu3\Service\Product\Properties;

class ProductSave
{
  private $payload;

  function __construct(string $json)
  {
    $this->payload = json_decode($json, true);
  }

  function getPayload() : array
  {
    return $this->payload;
  }

  function getPayloadProduct() : array
  {
    return $this->payload[Properties::PRODUCT];
  }

  function getPayloadStorage() : string
  {
    return $this->payload[Properties::STORAGE];
  }
}

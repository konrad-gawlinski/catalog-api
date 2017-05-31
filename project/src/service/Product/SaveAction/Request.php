<?php

namespace Nu3\Service\Product\SaveAction;

use Nu3\Service\Product\Property;

class Request
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
    return $this->payload[Property::PRODUCT];
  }

  function getPayloadStorage() : string
  {
    return $this->payload[Property::STORAGE];
  }
}

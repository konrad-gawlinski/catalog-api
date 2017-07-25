<?php

namespace Nu3\Service\Product;

class Request
{
  private $sku;
  private $payload;

  function __construct(string $sku, array $payload)
  {
    $this->sku = $sku;
    $this->payload = $payload;
  }

  function getPayload() : array
  {
    return $this->payload;
  }

  function getSku() : string
  {
    return $this->sku;
  }
}

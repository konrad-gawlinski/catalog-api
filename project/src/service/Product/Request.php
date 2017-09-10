<?php

namespace Nu3\Service\Product;

class Request
{
  private $sku;
  private $country;
  private $language;
  private $payload;

  function __construct(string $sku, string $country, string $language, array $payload)
  {
    $this->sku = $sku;
    $this->country = $country;
    $this->language = $language;
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

  function getCountry() : string
  {
    return $this->country;
  }

  function getLanguage() : string
  {
    return $this->language;
  }
}

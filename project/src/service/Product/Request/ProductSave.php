<?php

namespace Nu3\Service\Product\Request;

use Nu3\Config;
use Nu3\Property\Config as AppConfig;
use Nu3\Service\Product\Entity\Properties;
use Nu3\Core\Violation;
use Nu3\Service\Product\ErrorKey;

class ProductSave
{
  use AppConfig;

  private $storedProduct = [];
  private $violations = [];
  private $payload;

  function __construct(string $json)
  {
    $this->payload = json_decode($json, true);
  }

  function isValid() : bool
  {
    return empty($this->violations);
  }

  function getViolations() : array
  {
    return $this->violations;
  }
  
  function getPayloadProduct() : array
  {
    return $this->payload[Properties::PRODUCT];
  }

  function getPayloadStorage() : string
  {
    return $this->payload[Properties::STORAGE];
  }

  function setStoredProduct(array $product)
  {
    $this->storedProduct = $product;
  }

  function getStoredProduct() : array
  {
    return $this->storedProduct;
  }

  function preValidatePayload() : array
  {
    $violations = [];
    $availableStorage = $this->config()[Config::STORAGE][Config::STORAGE_AVAILABLE];

    if (empty($this->payload[Properties::PRODUCT][Properties::PRODUCT_SKU])) {
      $violations[] = new Violation(ErrorKey::SKU_IS_REQUIRED, Violation::EK_REQUEST);
    }

    if (empty($this->payload[Properties::STORAGE])) {
      $violations[] = new Violation(ErrorKey::STORAGE_IS_REQUIRED, Violation::EK_REQUEST);
    } else if (!in_array($this->payload[Properties::STORAGE], $availableStorage)) {
      $violations[] = new Violation(ErrorKey::INVALID_STORAGE_VALUE, Violation::EK_REQUEST);
    }

    array_merge($this->violations, $violations);

    return $violations;
  }

  function preValidateProduct(array $storedProduct) : array
  {
    $violations = [];
    if (!isset($storedProduct[Properties::PRODUCT_SKU])
      && empty($this->payload[Properties::PRODUCT][Properties::PRODUCT_TYPE]))
      $violations[] = new Violation(ErrorKey::NEW_PRODUCT_REQUIRES_TYPE, Violation::EK_REQUEST);

    array_merge($this->violations, $violations);

    return $violations;
  }
}
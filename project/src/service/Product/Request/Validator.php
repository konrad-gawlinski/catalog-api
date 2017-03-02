<?php

namespace Nu3\Service\Product\Request;

use Nu3\Config;
use Nu3\Property\Config as AppConfig;
use Nu3\Core\Violation;
use Nu3\Service\Product\Entity\Properties as Property;
use Nu3\Service\Product\ErrorKey;

class Validator
{
  use AppConfig;

  function validatePayload(ProductSave $request) : array
  {
    $violations = [];
    $payload = $request->getPayload();

    $violations += $this->validateRequiredSku($payload);
    $violations += $this->validateStorage($payload);

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateRequiredSku(array $payload) : array
  {
    if (empty($payload[Property::PRODUCT][Property::PRODUCT_SKU])) {
      return [new Violation(ErrorKey::SKU_IS_REQUIRED, Violation::ET_REQUEST)];
    }

    return [];
  }

  /**
   * @return Violation[]
   */
  private function validateStorage(array $payload) : array
  {
    $availableStorage = $this->config()[Config::STORAGE][Config::STORAGE_AVAILABLE];

    if (empty($payload[Property::STORAGE])) {
      return [new Violation(ErrorKey::STORAGE_IS_REQUIRED, Violation::ET_REQUEST)];
    } else if (!in_array($payload[Property::STORAGE], $availableStorage)) {
      return [new Violation(ErrorKey::INVALID_STORAGE_VALUE, Violation::ET_REQUEST)];
    }

    return [];
  }

  /**
   * @return Violation[]
   */
  function validateProduct(ProductSave $request) : array
  {
    $violations = [];
    $payload = $request->getPayloadProduct();

    $violations += $this->validateRequiredProductType($payload, $request->getStoredProduct());
    $violations += $this->validateProductType($payload);

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateRequiredProductType(array $payload, array $storedProduct) : array
  {
    if (!isset($storedProduct[Property::PRODUCT_SKU])
      && empty($payload[Property::PRODUCT_TYPE]))
      return [new Violation(ErrorKey::NEW_PRODUCT_REQUIRES_TYPE, Violation::ET_REQUEST)];

    return [];
  }

  /**
   * @return Violation[]
   */
  private function validateProductType(array $payload) : array
  {
    $availableProductTypes = array_keys($this->config()[Config::PRODUCT]);

    if (isset($payload[Property::PRODUCT_TYPE])
      && !in_array($payload[Property::PRODUCT_TYPE], $availableProductTypes))
      return [new Violation(ErrorKey::INVALID_PRODUCT_TYPE, Violation::ET_REQUEST)];

    return [];
  }
}

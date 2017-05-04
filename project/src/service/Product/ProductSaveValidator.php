<?php

namespace Nu3\Service\Product;

use Nu3\Config;
use Nu3\Feature\Config as AppConfig;
use Nu3\Core\Violation;

class ProductSaveValidator
{
  use AppConfig;

  function validateRequest(Request\ProductSave $request) : array
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
    if (empty($payload[Properties::PRODUCT][Properties::PRODUCT_SKU])) {
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

    if (empty($payload[Properties::STORAGE])) {
      return [new Violation(ErrorKey::STORAGE_IS_REQUIRED, Violation::ET_REQUEST)];
    } else if (!in_array($payload[Properties::STORAGE], $availableStorage)) {
      return [new Violation(ErrorKey::INVALID_STORAGE_VALUE, Violation::ET_REQUEST)];
    }

    return [];
  }

  /**
   * @return Violation[]
   */
  function validateProduct(DTO\ProductSave $dto, array $storedProductProperties) : array
  {
    $violations = [];
    $productProperties = $dto->getProductProperties();

    $violations += $this->validateRequiredProductType($productProperties, $storedProductProperties);
    $violations += $this->validateProductType($productProperties);

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateRequiredProductType(array $productProperties, array $storedProductProperties) : array
  {
    if (!isset($storedProductProperties[Properties::PRODUCT_SKU])
      && empty($productProperties[Properties::PRODUCT_TYPE]))
      return [new Violation(ErrorKey::NEW_PRODUCT_REQUIRES_TYPE, Violation::ET_REQUEST)];

    return [];
  }

  /**
   * @return Violation[]
   */
  private function validateProductType(array $productProperties) : array
  {
    $availableProductTypes = array_keys($this->config()[Config::PRODUCT]);

    if (isset($productProperties[Properties::PRODUCT_TYPE])
      && !in_array($productProperties[Properties::PRODUCT_TYPE], $availableProductTypes))
      return [new Violation(ErrorKey::INVALID_PRODUCT_TYPE, Violation::ET_REQUEST)];

    return [];
  }
}

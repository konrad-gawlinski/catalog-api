<?php

namespace Nu3\Service\Product\Action\CreateProduct;

use Nu3\Core\Violation;
use Nu3\Service\Product\Action\CUValidator;
use Nu3\Feature\Config as ConfigFeature;
use Nu3\Config;
use Nu3\Service\Product\ErrorKey;
use Nu3\Service\Product\Property;

class Validator extends CUValidator
{
  use ConfigFeature;

  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validateRequest($request) : array
  {
    $payload = $request->getPayload();
    $violations = $this->validateRequiredSku($payload);
    $violations = array_merge($violations, $this->validateProductType($payload));
    $violations = array_merge($violations, $this->makeSureProductDoesNotExist($payload));

    return $violations;
  }

  protected function validateRequiredSku(array $payload) : array
  {
    if (empty($payload[Property::PRODUCT_SKU])) {
      return [new Violation(ErrorKey::SKU_IS_REQUIRED)];
    }

    return [];
  }

  protected function validateProductType(array $payload) : array
  {
    $violations = $this->validateRequiredProductType($payload);
    if (!$violations)
      $violations = $this->validateAllowedProductType($payload);

    return $violations;
  }

  protected function validateRequiredProductType(array $payload) : array
  {
    if (empty($payload[Property::PRODUCT_TYPE]))
      return [new Violation(ErrorKey::NEW_PRODUCT_REQUIRES_TYPE)];

    return [];
  }

  protected function validateAllowedProductType(array $payload) : array
  {
    $availableProductTypes = array_keys($this->config()[Config::PRODUCT]);

    if (!in_array($payload[Property::PRODUCT_TYPE], $availableProductTypes))
      return [new Violation(ErrorKey::INVALID_PRODUCT_TYPE)];

    return [];
  }

  private function makeSureProductDoesNotExist(array $payload) : array
  {
    if (!empty($payload[Property::PRODUCT_SKU])) {
      $sku = $payload[Property::PRODUCT_SKU];

      if ($this->productGateway->productExists($sku))
        return [new Violation(ErrorKey::PRODUCT_CREATION_FORBIDDEN)];
    }

    return [];
  }
}

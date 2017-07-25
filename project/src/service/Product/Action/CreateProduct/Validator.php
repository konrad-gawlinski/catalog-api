<?php

namespace Nu3\Service\Product\Action\CreateProduct;

use Nu3\Config;
use Nu3\Core\Violation;
use Nu3\Service\Product\Request;
use Nu3\Service\Product\Property;
use Nu3\Service\Product\ErrorKey;

class Validator extends \Nu3\Service\Product\Action\Validator
{
  function validateRequest(Request $request) : array
  {
    $violations = [];
    $payload = $request->getPayload();
    $violations += $this->validateRequiredSku($request->getSku());
    $violations += $this->validateProductType($payload);

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateProductType(array $payload) : array
  {
      $violations = [];
      $violations += $this->validateRequiredProductType($payload);
      if (!$violations)
        $violations += $this->validateAllowedProductType($payload);

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateRequiredProductType(array $payload) : array
  {
    if (empty($payload[Property::PRODUCT_TYPE]))
      return [new Violation(ErrorKey::NEW_PRODUCT_REQUIRES_TYPE)];

    return [];
  }

  /**
   * @return Violation[]
   */
  private function validateAllowedProductType(array $payload) : array
  {
    $availableProductTypes = array_keys($this->config()[Config::PRODUCT]);

    if (!in_array($payload[Property::PRODUCT_TYPE], $availableProductTypes))
      return [new Violation(ErrorKey::INVALID_PRODUCT_TYPE)];

    return [];
  }
}

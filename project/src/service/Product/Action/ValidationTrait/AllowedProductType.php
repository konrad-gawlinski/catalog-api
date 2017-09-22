<?php

namespace Nu3\Service\Product\Action\ValidationTrait;

use Nu3\Config;
use Nu3\Core\Violation;
use Nu3\Service\Product\Property;
use Nu3\Service\Product\ErrorKey;

trait AllowedProductType
{
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
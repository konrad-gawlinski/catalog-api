<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Core\Violation;
use Nu3\Service\Product\Action\CURequest as Request;
use Nu3\Service\Product\Action\CUValidator;
use Nu3\Service\Product\Property;

class Validator extends CUValidator
{
  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validateRequest($request) : array
  {
    $violations = parent::validateRequest($request);
    $violations = array_merge($violations, $this->validateProductType($request->getPayload()));

    return $violations;
  }

  /**
   * @return Violation[]
   */
  protected function validateProductType(array $payload) : array
  {
    $violations = [];
    if (isset($payload[Property::PRODUCT_TYPE])) {
      $violations += $this->validateAllowedProductType($payload);
    }

    return $violations;
  }
}

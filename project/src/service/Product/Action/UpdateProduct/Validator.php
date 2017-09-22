<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Core\Violation;
use Nu3\Service\Product\Action\CURequest as Request;
use Nu3\Service\Product\Action\ValidationTrait;
use Nu3\Service\Product\Property;

class Validator extends \Nu3\Service\Product\Action\Validator
{
  use ValidationTrait\AllowedProductType;
  
  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validateRequest($request) : array
  {
    $violations = parent::validateRequest($request);
    $violations += $this->validateProductType($request->getPayload());

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateProductType(array $payload) : array
  {
    $violations = [];
    if (isset($payload[Property::PRODUCT_TYPE])) {
      $violations += $this->validateAllowedProductType($payload);
    }

    return $violations;
  }
}

<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Core\Violation;
use Nu3\Service\Product\Action\CUValidator;
use Nu3\Service\Product\ErrorKey;
use Nu3\Service\Product\Feature\RequiredIdValidator;
use Nu3\Service\Product\Property;

class Validator extends CUValidator
{
  use RequiredIdValidator;

  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validateRequest($request) : array
  {
    $violations = $this->validateRequiredId($request->getId());
    $violations = array_merge($violations, $this->rejectEmptyBody($request->getPayload()));

    return $violations;
  }

  private function rejectEmptyBody(array $payload)
  {
    if (!isset($payload[Property::PRODUCT_PROPERTIES])) {
      return [new Violation(ErrorKey::EMPTY_PRODUCT_PROPERTIES)];
    }

    return [];
  }
}

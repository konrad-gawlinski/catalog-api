<?php

namespace Nu3\Service\Product\Action\GetProduct;

use Nu3\Feature\Config as AppConfig;
use Nu3\Service\Product\Action\GetRequest;
use Nu3\Core\Violation;
use Nu3\Service\Product\ErrorKey;

class Validator implements \Nu3\Service\Product\Action\Validator
{
  use AppConfig;

  /**
   * @param GetRequest $request
   *
   * @return Violation[] array
   */
  function validateRequest($request) : array
  {
    $violations = $this->validateRequiredId($request->getId());

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateRequiredId(string $productId) : array
  {
    if (!$productId) {
      return [new Violation(ErrorKey::ID_IS_REQUIRED)];
    }

    if (!intval($productId)) {
      return [new Violation(ErrorKey::ID_HAS_TO_BE_A_NUMBER)];
    }

    return [];
  }
}

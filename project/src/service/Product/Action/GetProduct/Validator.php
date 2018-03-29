<?php

namespace Nu3\Service\Product\Action\GetProduct;

use Nu3\Feature\Config as AppConfig;
use Nu3\Service\Product\ErrorKey;
use Nu3\Core\Violation;

class Validator implements \Nu3\Service\Product\Action\Validator
{
  use AppConfig;

  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validateRequest($request) : array
  {
    $violations = $this->validateRequiredId($request->getId());

    return $violations;
  }

  protected function validateRequiredId(string $productId) : array
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

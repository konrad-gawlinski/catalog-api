<?php

namespace Nu3\Service\Product\Action;

use Nu3\Feature\Config as AppConfig;
use Nu3\Service\Product\Request;
use Nu3\Core\Violation;
use Nu3\Service\Product\ErrorKey;

class Validator
{
  use AppConfig;

  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validateRequest($request) : array
  {
    $violations = [];

    $violations += $this->validateRequiredSku($request->getSku());

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateRequiredSku(string $sku) : array
  {
    if (empty($sku)) {
      return [new Violation(ErrorKey::SKU_IS_REQUIRED)];
    }

    return [];
  }
}

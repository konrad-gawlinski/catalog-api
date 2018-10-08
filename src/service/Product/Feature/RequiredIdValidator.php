<?php

namespace Nu3\Service\Product\Feature;

use Nu3\Core\Violation;
use Nu3\Service\Product\ErrorKey;

trait RequiredIdValidator
{
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
<?php

namespace Nu3\Service\Product\GetAction;

use Nu3\Config;
use Nu3\Feature\Config as AppConfig;
use Nu3\Core\Violation;
use Nu3\Service\Product\ErrorKey;

class Validator
{
  use AppConfig;

  function validateRequest(Request $request) : array
  {
    $violations = [];
    $sku = $request->sku();
    $storage = $request->storage();

    $violations += $this->validateRequiredSku($sku);
    $violations += $this->validateStorage($storage);

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateRequiredSku(string $sku) : array
  {
    if (empty($sku)) {
      return [new Violation(ErrorKey::SKU_IS_REQUIRED, Violation::ET_REQUEST)];
    }

    return [];
  }

  /**
   * @return Violation[]
   */
  private function validateStorage(string $storage) : array
  {
    $availableStorage = $this->config()[Config::STORAGE][Config::STORAGE_AVAILABLE];

    if (empty($storage)) {
      return [new Violation(ErrorKey::STORAGE_IS_REQUIRED, Violation::ET_REQUEST)];
    } else if (!in_array($storage, $availableStorage)) {
      return [new Violation(ErrorKey::INVALID_STORAGE_VALUE, Violation::ET_REQUEST)];
    }

    return [];
  }
}

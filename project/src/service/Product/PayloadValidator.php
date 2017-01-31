<?php

namespace Nu3\Service\Product;

use Nu3\Core\JsonValidator;
use Nu3\Core\Violation;
use Nu3\Service\Product\Entity\Properties;

class PayloadValidator extends JsonValidator
{
  function preValidatePayload(array $data) : array
  {
    $violations = [];

    if (empty($data[Properties::PRODUCT][Properties::PRODUCT_SKU])) {
      $violations[] = new Violation(ErrorKey::SKU_IS_REQUIRED, Violation::EK_REQUEST);
    }

    if (empty($data[Properties::STORAGE])) {
      $violations[] = new Violation(ErrorKey::STORAGE_IS_REQUIRED, Violation::EK_REQUEST);
    }

    return $violations;
  }

  function validatePayload(array $data)
  {
    $schema = $this->chooseSchema($data[Properties::PRODUCT][Properties::PRODUCT_TYPE]);
    $this->validate($data, $schema);
  }

  private function chooseSchema(string $productType) : string
  {
    switch ($productType) {
      case 'config': return APPLICATION_SRC . 'service/Product/config/validation/rest-request/product.json';
    }

    throw new Exception(ErrorKey::INVALID_PRODUCT_PAYLOAD_VALIDATION_FILE_PATH);
  }
}
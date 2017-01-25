<?php

namespace Nu3\Service\Product;

use Nu3\Core\JsonValidator;
use Nu3\Service\Product\Entity\Properties;

class PayloadValidator extends JsonValidator
{
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
<?php

namespace Nu3\Service\Product;

use Nu3\Core\Validator;
use Nu3\Service\Product\Entity\Properties;
use Symfony\Component\Validator\ConstraintViolation;

class EntityValidator extends Validator
{
  function validate(Entity\Product $product)
  {
    $violations = $this
      ->buildValidator($this->chooseValidationRules($product->properties[Properties::PRODUCT_TYPE]))
      ->validate($product);

    /** @var ConstraintViolation $violation */
    foreach ($violations as $violation) {
      var_dump($violation);
    }
  }

  private function chooseValidationRules(string $productType) : string
  {
    switch ($productType) {
      case 'config': return APPLICATION_SRC . 'service/Product/config/validation/entity/product.yml';
    }

    throw new Exception(ErrorKey::INVALID_PRODUCT_VALIDATION_RULES_FILE_PATH);
  }
}
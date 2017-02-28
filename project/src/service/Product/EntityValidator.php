<?php

namespace Nu3\Service\Product;

use Nu3\Core\Validator;
use Nu3\Core\Violation;
use Nu3\Service\Product\Entity\Properties;
use Symfony\Component\Validator\ConstraintViolation;

class EntityValidator extends Validator
{
  /**
   * @throws Exception
   * @return Violation[]
   */
  function validate(Entity\Product $product) : array
  {
    $requestViolations = [];
    $violations = $this
      ->buildValidator($this->chooseValidationRules($product->properties[Properties::PRODUCT_TYPE]))
      ->validate($product);

    /** @var ConstraintViolation $violation */
    foreach ($violations as $violation) {
      $requestViolations[] = new Violation($this->buildMessage($violation), Violation::ET_REQUEST);
    }

    return $requestViolations;
  }

  /**
   * @throws Exception
   */
  private function chooseValidationRules(string $productType) : string
  {
    switch ($productType) {
      case 'config': return APPLICATION_SRC . 'service/Product/config/validation/entity/product.yml';
    }

    throw new Exception(ErrorKey::INVALID_PRODUCT_VALIDATION_RULES_FILE_PATH, Violation::ET_REQUEST);
  }

  private function buildMessage(ConstraintViolation $violation) : string
  {
    return $violation->getMessage() . " {$violation->getPropertyPath()}";
  }
}
<?php

namespace Nu3\Service\Product;

use Nu3\Core;
use Symfony\Component\Validator\ConstraintViolation;

class EntityValidator extends Core\Validator
{
  use \Nu3\Feature\Config;

  const VALIDATION_RULES_DIR = APPLICATION_SRC . 'service/Product/validation_rules/';
  
  /**
   * @return Core\Violation[]
   */
  function validate(Entity\Product $product) : array
  {
    var_dump($product);
    $requestViolations = [];
    $violations = $this
      ->buildValidator($this->chooseValidationRules($product->properties[Properties::PRODUCT_TYPE]))
      ->validate($product);

    /** @var ConstraintViolation $violation */
    foreach ($violations as $violation) {
      $requestViolations[] = new Core\Violation($this->buildMessage($violation), Core\Violation::ET_REQUEST);
    }

    return $requestViolations;
  }

  /**
   * @throws Exception
   */
  private function chooseValidationRules(string $productType) : string
  {
    $fileName = $this->config()[\Nu3\Config::PRODUCT][$productType][\Nu3\Config::VALIDATION_RULES];

    return self::VALIDATION_RULES_DIR . $fileName;
  }

  private function buildMessage(ConstraintViolation $violation) : string
  {
    return $violation->getMessage() . " {$violation->getPropertyPath()}";
  }
}

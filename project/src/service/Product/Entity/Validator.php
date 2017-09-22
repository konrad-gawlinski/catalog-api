<?php

namespace Nu3\Service\Product\Entity;

use Nu3\Core;
use Nu3\Service\Product\ErrorKey;
use Nu3\Service\Product\Exception;
use Symfony\Component\Validator\ConstraintViolation;

class Validator extends Core\Validator
{
  use \Nu3\Feature\Config;

  const VALIDATION_RULES_DIR = APPLICATION_SRC . 'service/Product/validation_rules/';
  
  /**
   * @return Core\Violation[]
   */
  function validate(Product $product) : array
  {
    $productViolations = [];
    $violations = $this
      ->buildValidator($this->chooseValidationRules($product->type))
      ->validate($product);

    /** @var ConstraintViolation $violation */
    foreach ($violations as $violation) {
      $productViolations[] = new Core\Violation(ErrorKey::PRODUCT_VALIDATION_ERROR, $this->buildMessage($violation));
    }

    return $productViolations;
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

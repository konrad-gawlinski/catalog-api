<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Core\Violation;
use Nu3\Service\Product\Action\CUValidator;
use Nu3\Service\Product\Feature\RequiredIdValidator;

class Validator extends CUValidator
{
  use RequiredIdValidator;

  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validateRequest($request) : array
  {
    return $this->validateRequiredId($request->getId());
  }
}

<?php

namespace Nu3\Service\Product\Action\GetProduct;

use Nu3\Core\Violation;
use Nu3\Service\Product\Feature\RequiredIdValidator;

class Validator implements \Nu3\Service\Product\Action\Validator
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

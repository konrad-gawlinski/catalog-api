<?php

namespace Nu3\Service\Product\Action\GetProduct;

use Nu3\Core\Violation;
use Nu3\Service\Product\Feature\RequiredIdValidator;

class RequestValidator implements \Nu3\Service\Product\Action\RequestValidator
{
  use RequiredIdValidator;

  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validate($request) : array
  {
    return $this->validateRequiredId($request->getId());
  }


}

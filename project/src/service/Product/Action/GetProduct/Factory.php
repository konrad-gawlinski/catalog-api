<?php

namespace Nu3\Service\Product\Action\GetProduct;

class Factory extends \Nu3\Service\Product\Factory
{
  function createRequestValidator() : RequestValidator
  {
    $object = new RequestValidator();

    return $object;
  }
}

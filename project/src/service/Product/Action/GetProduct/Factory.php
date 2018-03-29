<?php

namespace Nu3\Service\Product\Action\GetProduct;

class Factory extends \Nu3\Service\Product\Action\Factory
{
  function createValidator() : Validator
  {
    $object = new Validator();

    return $object;
  }
}

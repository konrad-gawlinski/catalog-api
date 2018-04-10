<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

class Factory extends \Nu3\Service\Product\Factory
{
  function createValidator() : Validator
  {
    $object = new Validator();

    return $object;
  }
}

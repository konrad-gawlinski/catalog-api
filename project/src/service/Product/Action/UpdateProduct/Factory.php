<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

class Factory extends \Nu3\Service\Product\Action\Factory
{
  function createValidator() : Validator
  {
    $object = new Validator();
    $object->setConfig($this->config());

    return $object;
  }
}

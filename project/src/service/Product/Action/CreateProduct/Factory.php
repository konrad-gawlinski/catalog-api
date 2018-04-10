<?php

namespace Nu3\Service\Product\Action\CreateProduct;

class Factory extends \Nu3\Service\Product\Factory
{
  /**
   * @return Builder
   */
  function createEntityBuilder()
  {
    $entityBuilder =  new Builder();
    $entityBuilder->setConfig($this->config());

    return $entityBuilder;
  }

  function createValidator() : Validator
  {
    $object = new Validator();
    $object->setConfig($this->config());

    return $object;
  }
}

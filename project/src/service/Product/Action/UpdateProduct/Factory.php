<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

class Factory extends \Nu3\Service\Product\Factory
{
  function createDataTransferObject()
  {
    return new TransferObject();
  }

  function createRequestValidator() : RequestValidator
  {
    $object = new RequestValidator();

    return $object;
  }

  function createProductValidator() : Validator\ProductValidator
  {
    $object = new Validator\ProductValidator($this);
    $object->setConfig($this->config());
    $object->setRegionUtils($this->regionUtils());

    return $object;
  }

  function createProductSimpleValidator() : Validator\ProductSimple
  {
    return new Validator\ProductSimple();
  }
}

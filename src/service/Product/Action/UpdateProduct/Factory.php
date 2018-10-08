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
}

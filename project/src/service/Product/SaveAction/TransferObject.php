<?php

namespace Nu3\Service\Product\SaveAction;

class TransferObject
{
  /** @var array */
  private $productProperties;
  /** @var string  */
  private $storage;

  /** @var bool  */
  private $isNew = false;

  function __construct(Request $request)
  {
    $this->productProperties = $request->getPayloadProduct();
    $this->storage = $request->getPayloadStorage();
  }

  function getProductProperties() : array
  {
    return $this->productProperties;
  }

  function setProductProperties(array $properties)
  {
    $this->productProperties = $properties;
  }

  function getStorage() : string
  {
    return $this->storage;
  }

  function setIsNew(bool $isNew)
  {
    $this->isNew = $isNew;
  }

  function getIsNew() : bool
  {
    return $this->isNew;
  }
}

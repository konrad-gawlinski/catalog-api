<?php

namespace Nu3\Service\Product;

class TransferObject
{
  /** @var string */
  private $sku;

  /** @var array */
  private $productProperties;

  function __construct(Request $request)
  {
    $this->sku = $request->getSku();
    $this->productProperties = $request->getPayload();
  }

  function getProductProperties() : array
  {
    return $this->productProperties;
  }

  function setProductProperties(array $properties)
  {
    $this->productProperties = $properties;
  }

  function getSku() : string
  {
    return $this->sku;
  }
}

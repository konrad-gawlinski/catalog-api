<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Action\CURequest;

class TransferObject
{
  /** @var string */
  private $sku;
  private $type;

  /** @var array */
  private $productProperties = [];

  function __construct(CURequest $request)
  {
    $payload = $request->getPayload();

    $this->sku = $request->getSku();
    if (isset($payload[Property::PRODUCT_TYPE]))
      $this->type = $payload[Property::PRODUCT_TYPE];

    if (isset($payload[Property::PRODUCT_PROPERTIES]))
      $this->productProperties = $payload[Property::PRODUCT_PROPERTIES];
  }

  function getProductProperties() : array
  {
    return $this->productProperties;
  }

  function getSku() : string
  {
    return $this->sku;
  }

  function getType() : string
  {
    return $this->type;
  }
}

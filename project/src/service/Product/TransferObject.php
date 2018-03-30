<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Feature\RequestPayload;

class TransferObject
{
  /** @var string */
  private $sku = '';
  private $type = '';

  /** @var array */
  private $productProperties = [];

  /**
   * @param RequestPayload $request
   */
  function __construct($request)
  {
    $payload = $request->getPayload();

    if (isset($payload[Property::PRODUCT_SKU]))
      $this->sku = $payload[Property::PRODUCT_SKU];

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

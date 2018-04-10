<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Feature\RequestPayload;

class TransferObject
{
  /** @var string */
  public $sku = '';
  public $type = '';

  /** @var array */
  public $properties = [];

  /**
   * @param RequestPayload $request
   */
  function applyRequestProperties($request)
  {
    $payload = $request->getPayload();

    if (isset($payload[Property::PRODUCT_SKU]))
      $this->sku = $payload[Property::PRODUCT_SKU];

    if (isset($payload[Property::PRODUCT_TYPE]))
      $this->type = $payload[Property::PRODUCT_TYPE];

    if (isset($payload[Property::PRODUCT_PROPERTIES]))
      $this->properties = $payload[Property::PRODUCT_PROPERTIES];
  }
}

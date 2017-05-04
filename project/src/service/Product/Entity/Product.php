<?php

namespace Nu3\Service\Product\Entity;

use Nu3\Service\Product\DTO;
use Nu3\Service\Product\Properties;

class Product
{
  public $sku = '';
  public $status = '';
  public $properties = [];

  function fillFromDto(DTO\ProductSave $dto)
  {
    $productProperties = $dto->getProductProperties();
    $this->sku = $productProperties[Properties::PRODUCT_SKU];
    $this->status = $productProperties[Properties::PRODUCT_STATUS];

    $properties = $productProperties;
    unset($properties[Properties::PRODUCT_SKU]);
    unset($properties[Properties::PRODUCT_STATUS]);

    $this->properties = $properties;
  }
}

<?php

namespace Nu3\Service\Product\Entity;

use Nu3\Service\Product\SaveAction\TransferObject;
use Nu3\Service\Product\Property;

class Product
{
  public $sku = '';
  public $status = '';
  public $properties = [];

  function fillFromDto(TransferObject $dto)
  {
    $productProperties = $dto->getProductProperties();
    $this->sku = $productProperties[Property::PRODUCT_SKU];
    $this->status = $productProperties[Property::PRODUCT_STATUS];

    $properties = $productProperties;
    unset($properties[Property::PRODUCT_SKU]);
    unset($properties[Property::PRODUCT_STATUS]);

    $this->properties = $properties;
  }

  function fillFromArray(array $product)
  {
    $this->sku = $product[Property::PRODUCT_SKU];
    $this->status = $product[Property::PRODUCT_STATUS];
    $this->properties = $product[Property::PROPERTIES];
  }
}

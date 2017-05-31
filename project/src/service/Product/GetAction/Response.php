<?php

namespace Nu3\Service\Product\GetAction;

use Nu3\Service\Product\Entity\Product as ProductEntity;
use Nu3\Service\Product\Property;

class Response
{
  private $properties = [];

  function __construct(ProductEntity $product)
  {
    $this->fillProperties($product);
  }

  function getProperties() : array
  {
    return $this->properties;
  }

  private function fillProperties(ProductEntity $product)
  {
    $this->properties = json_decode($product->properties, true);
    $this->properties = [
      Property::PRODUCT_SKU => $product->sku,
      Property::PRODUCT_STATUS => $product->status
    ] + $this->properties;
  }
}

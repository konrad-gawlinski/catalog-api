<?php

namespace Nu3\Service\Product\Action\GetProduct;

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
    $this->properties = [
      Property::PRODUCT_ID => $product->id,
      Property::PRODUCT_SKU => $product->sku,
      Property::PRODUCT_TYPE => $product->type,
    ] + $product->properties;
  }
}

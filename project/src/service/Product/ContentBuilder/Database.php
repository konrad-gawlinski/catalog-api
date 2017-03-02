<?php

namespace Nu3\Service\Product\ContentBuilder;

use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\Entity\Properties;

class Database
{
  function build(Product $product) : string
  {
    $properties = $product->properties;
    $properties[Properties::PRODUCT_TYPE] = $product->type;
    unset($properties[Properties::PRODUCT_STATUS]);
    unset($properties[Properties::PRODUCT_SKU]);

    return json_encode($properties);
  }
}
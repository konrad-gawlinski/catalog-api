<?php

namespace Nu3\Service\Product\Entity;

class DatabaseConverter
{
  function toDatabase(Product $product) : string
  {
    $properties = $product->properties;
    $properties[Properties::PRODUCT_TYPE] = $product->type;
    unset($properties[Properties::PRODUCT_STATUS]);
    unset($properties[Properties::PRODUCT_SKU]);

    return json_encode($properties);
  }
}
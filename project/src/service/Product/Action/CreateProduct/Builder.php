<?php

namespace Nu3\Service\Product\Action\CreateProduct;

use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\Entity\ProductStatus;
use Nu3\Service\Product\EntityBuilder;
use Nu3\Service\Product\Property;

class Builder extends EntityBuilder
{
  function applyDefaultAttributesValues(Product $product) : Product
  {
    $this->forceStatus_new($product);

    return $product;
  }

  private function forceStatus_new(Product $product)
  {
    foreach ($product->properties as &$region) {
      $region[Property::PRODUCT_STATUS] = ProductStatus::NEW;
    }
  }
}

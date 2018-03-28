<?php

namespace Nu3\Service\Product\Action\CreateProduct;

use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\Entity\ProductStatus;
use Nu3\Service\Product\Property;

class Builder extends \Nu3\Service\Product\Entity\Builder
{
  function applyDefaultAttributesValues(Product $entity)
  {
    $this->forceStatus_new($entity);
  }

  private function forceStatus_new(Product $entity)
  {
    foreach ($entity->properties as &$region) {
      $region[Property::PRODUCT_STATUS] = ProductStatus::NEW;
    }
  }
}

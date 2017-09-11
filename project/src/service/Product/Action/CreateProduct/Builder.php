<?php

namespace Nu3\Service\Product\Action\CreateProduct;

use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\Entity\ProductStatus;
use Nu3\Service\Product\Property;

class Builder extends \Nu3\Service\Product\Entity\Builder
{
  function applyDefaultAttributesValues(Product $entity)
  {
    $entity->properties[Property::PRODUCT_STATUS] = ProductStatus::NEW;
  }
}

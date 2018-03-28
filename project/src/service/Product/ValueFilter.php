<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Entity\Product;

class ValueFilter
{
  function filterEntity(Product $product) : Product
  {
    $properties = &$product->properties;

    $this->applyFilter($properties, Property::PRODUCT_NAME, 'trim');
    $this->applyFilter($properties, Property::META_TITLE, 'trim');

    return $product;
  }

  private function applyFilter(array &$properties, $propertyName, callable $filter)
  {
    foreach ($properties as $region => &$regionProperties) {
        if (isset($regionProperties[$propertyName])) {
          $propertyValue = $regionProperties[$propertyName];
          $regionProperties[$propertyName] = call_user_func($filter, $propertyValue);
        }
    }
  }
}

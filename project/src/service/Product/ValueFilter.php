<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Entity\Product;

class ValueFilter
{
  function filterEntity(Product $product) : Product
  {
    $this->applyFilter($product, Property::PRODUCT_NAME, 'trim');
    $this->applyFilter($product, Property::META_TITLE, 'trim');

    return $product;
  }

  private function applyFilter(Product $product, $propertyName, callable $filter)
  {
    $property = &$product->properties;

    if (isset($property[$propertyName])) {
      $property[$propertyName] = call_user_func($filter, ($property[$propertyName]));
    }
  }
}

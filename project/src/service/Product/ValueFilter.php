<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Entity\Product;

class ValueFilter
{
  function filterEntity(Product $product)
  {
    $this->applyFilter($product, Property::PRODUCT_NAME, 'trim');
    $this->applyFilter($product, Property::META_TITLE, 'trim');
  }

  private function applyFilter(Product $product, $propertyName, callable $filter)
  {
    $property = &$product->attributes;

    if (isset($property[$propertyName])) {
      $property[$propertyName] = call_user_func($filter, ($property[$propertyName]));
    }
  }
}

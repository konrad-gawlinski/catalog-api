<?php

namespace Nu3\Service\Product\SaveAction;

use Nu3\Service\Product\Property;
use Nu3\Service\Product\Entity\Product;

class ValueFilter
{
  function filterEntity(Product $product)
  {
    $this->applyFilter($product, Property::PRODUCT_NAME, 'trim');
    $this->applyFilter($product, Property::SEO .'.'. Property::META_TITLE, 'trim');
  }

  private function applyFilter(Product $product, string $propertyPath, callable $filter)
  {
    $propertyTokens = explode('.', $propertyPath);
    $tokensCount = count($propertyTokens)-1;

    $property = &$product->properties;
    for ($i=0; $i < $tokensCount; ++$i) {
      $key = $propertyTokens[$i];
      if (!isset($property[$key])) return;

      $property = &$property[$key];
    }

    $key = $propertyTokens[$tokensCount];
    if (!isset($property[$key])) return;
    $property[$key] = call_user_func($filter, ($property[$key]));
  }
}

<?php

namespace spec\Product\Nu3\Service\Product;

use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\Property;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValueFilterSpec extends ObjectBehavior
{
  function it_should_apply_filter(Product $product)
  {
    $product->properties = ['global' => [
      Property::PRODUCT_NAME => '  some product__name  ',
      Property::META_TITLE => '  meta-title  ',
    ]];

    $product = $this->filterEntity($product);

    $product->__get('properties')->shouldReturn(['global' => [
      Property::PRODUCT_NAME => 'some product__name',
      Property::META_TITLE => 'meta-title',
    ]]);
  }
}

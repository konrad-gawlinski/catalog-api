<?php

namespace spec\Product\Nu3\Service\Product\Action\CreateProduct;

use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\Entity\ProductStatus;
use Nu3\Service\Product\Property as P;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BuilderSpec extends ObjectBehavior
{
  function it_should_force_status_new_for_all_regions(Product $product)
  {
    $input = [
      'global' => [
        P::PRODUCT_NAME => 'somename'
      ],
      'de' => [
        P::PRODUCT_PRICE => '210'
      ]
    ];

    $expected = $input;
    $expected['global'][P::PRODUCT_STATUS] = ProductStatus::NEW;
    $expected['de'][P::PRODUCT_STATUS] = ProductStatus::NEW;

    $this->assert($product, $input, $expected);
  }

  function it_should_overwrite_status_for_all_regions(Product $product)
  {
    $input = [
      'global' => [
        P::PRODUCT_NAME => 'somename',
        P::PRODUCT_STATUS => 'sellable'
      ],
      'de' => [
        P::PRODUCT_PRICE => '210',
        P::PRODUCT_STATUS => 'listed'
      ]
    ];

    $expected = $input;
    $expected['global'][P::PRODUCT_STATUS] = ProductStatus::NEW;
    $expected['de'][P::PRODUCT_STATUS] = ProductStatus::NEW;

    $this->assert($product, $input, $expected);
  }

  private function assert(Product $product, $input, $expected)
  {
    $product->properties = $input;
    $_product = $this->applyDefaultAttributesValues($product);
    $_product->__get(P::PRODUCT_PROPERTIES)->shouldReturn($expected);
  }
}

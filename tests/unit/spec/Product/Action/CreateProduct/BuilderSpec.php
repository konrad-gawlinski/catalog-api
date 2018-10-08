<?php

namespace spec\Product\Nu3\Service\Product\Action\CreateProduct;

use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\Entity\ProductStatus;
use Nu3\Service\Product\Property as P;
use Nu3\Core\RegionUtils;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BuilderSpec extends ObjectBehavior
{
  function let()
  {
    $this->setRegionUtils(new RegionUtils());
  }

  function it_should_force_status_new_for_specific_regions(Product $product)
  {
    $input = [
      'global' => [
        P::PRODUCT_NAME => 'somename'
      ],
      'de' => [
        P::PRODUCT_PRICE => '210'
      ],
      'de_de' => [
        P::PRODUCT_META_TITLE => 'the coolest ever'
      ]
    ];

    $expected = $input;
    $expected['global'][P::PRODUCT_STATUS] = ProductStatus::NEW;
    $expected['de'][P::PRODUCT_STATUS] = ProductStatus::NEW;

    $this->assert($product, $input, $expected);
  }

  function it_should_not_set_status_new_for_language_regions(Product $product)
  {
    $input = [
      'global' => [
        P::PRODUCT_NAME => 'somename'
      ],
      'de' => [
        P::PRODUCT_PRICE => '210'
      ],
      'de_de' => [
        P::PRODUCT_META_TITLE => 'the coolest ever'
      ]
    ];

    $expected = $input;
    $expected['global'][P::PRODUCT_STATUS] = ProductStatus::NEW;
    $expected['de'][P::PRODUCT_STATUS] = ProductStatus::NEW;

    $this->assert($product, $input, $expected);
  }

  function it_should_overwrite_status_for_specific_regions(Product $product)
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

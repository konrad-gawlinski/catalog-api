<?php

namespace spec\Product\Nu3\Service\Product;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AttributeSorterSpec extends ObjectBehavior
{
  function it_should_sort_attributes()
  {
    $attributes = [
      'status' => 'approved',
      'name' => 'Some name',
      'tax_rate' => 19,
      'weight' => 21,
      'weight_unit' => 'kg',
      'description' => 'some description',
    ];

    $this->sort('de', 'de_de', $attributes)->shouldReturn([
      'de' => [
        'status' => 'approved',
        'tax_rate' => 19,
      ],
      'de_de' => [
        'name' => 'Some name',
        'description' => 'some description',
      ],
      'global' => [
        'weight' => 21,
        'weight_unit' => 'kg',
      ]
    ]);
  }
}

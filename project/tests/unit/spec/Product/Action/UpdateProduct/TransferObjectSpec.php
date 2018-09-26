<?php

namespace spec\Product\Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Service\Product\Action\UpdateProduct\Request as UpdateProductRequest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransferObjectSpec extends ObjectBehavior
{
  function it_should_return_correct_getter_values_for_create_product(UpdateProductRequest $request)
  {
    $request->getPayload()->willReturn(['properties' => ['property' => 'value']]);
    $request->getId()->willReturn('11');

    $this->applyRequestProperties($request);

    $this->__get('properties')->shouldReturn(['property' => 'value']);
    $this->__get('id')->shouldReturn('11');
  }
}

<?php

namespace spec\Product\Nu3\Service\Product;

use Nu3\Service\Product\Action\CreateProduct\Request as CreateProductRequest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransferObjectSpec extends ObjectBehavior
{
  function it_should_return_correct_getter_values_for_create_product(CreateProductRequest $request)
  {
    $request->getPayload()->willReturn(['properties' => ['property' => 'value']]);

    $this->beConstructedWith($request);

    $this->getProductProperties()->shouldReturn(['property' => 'value']);
  }
}

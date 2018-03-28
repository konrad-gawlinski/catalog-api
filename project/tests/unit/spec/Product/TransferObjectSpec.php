<?php

namespace spec\Product\Nu3\Service\Product;

use Nu3\Service\Product\Action\CURequest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransferObjectSpec extends ObjectBehavior
{
  function it_should_return_correct_getter_values(CURequest $request)
  {
    $request->getSku()->willReturn('nu3_123');
    $request->getPayload()->willReturn(['properties' => ['property' => 'value']]);

    $this->beConstructedWith($request);

    $this->getSku()->shouldReturn('nu3_123');
    $this->getProductProperties()->shouldReturn(['property' => 'value']);
  }
}

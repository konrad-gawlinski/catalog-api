<?php

namespace spec\Product\Nu3\Service\Product\Action;

use Nu3\Service\Product\Action\CURequest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CURequestSpec extends ObjectBehavior
{
  function it_should_return_correct_params()
  {
    $payload = [
      'some' => 'value'
    ];

    $this->beConstructedWith([
      CURequest::PROPERTY_SKU => 'nu3_123',
      CURequest::PROPERTY_PAYLOAD => $payload
    ]);

    $this->getSku()->shouldReturn('nu3_123');
    $this->getPayload()->shouldReturn($payload);
  }
}

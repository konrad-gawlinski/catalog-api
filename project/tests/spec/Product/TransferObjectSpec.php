<?php

namespace spec\Product\Nu3\Service\Product;

use Nu3\Service\Product\Action\CURequest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransferObjectSpec extends ObjectBehavior
{
  function it_should_return_correct_getter_values(CURequest $request)
  {
    $request->getCountry()->willReturn('de');
    $request->getSku()->willReturn('nu3_123');
    $request->getLanguage()->willReturn('de_de');
    $request->getPayload()->willReturn(['property' => 'value']);

    $this->beConstructedWith($request);

    $this->getCountry()->shouldReturn('de');
    $this->getSku()->shouldReturn('nu3_123');
    $this->getLanguage()->shouldReturn('de_de');
    $this->getProductProperties()->shouldReturn(['property' => 'value']);
  }
}

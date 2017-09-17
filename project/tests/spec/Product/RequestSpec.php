<?php

namespace spec\Product\Nu3\Service\Product;

use Nu3\Service\Product\Request;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestSpec extends ObjectBehavior
{
  function it_is_initializable()
  {
    $this->beConstructedWith([]);
    $this->shouldHaveType(Request::class);
  }

  function it_should_return_correct_params()
  {
    $this->beConstructedWith([
      Request::PROPERTY_SKU => 'nu3_123',
      Request::PROPERTY_COUNTRY => 'de',
      Request::PROPERTY_LANGUAGE => 'de_de'
    ]);

    $this->getSku()->shouldReturn('nu3_123');
    $this->getCountry()->shouldReturn('de');
    $this->getLanguage()->shouldReturn('de_de');
  }
}

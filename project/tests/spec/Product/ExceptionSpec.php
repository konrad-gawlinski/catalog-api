<?php

namespace spec\Product\Nu3\Service\Product;

use Nu3\Service\Product\Exception;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ExceptionSpec extends ObjectBehavior
{
  function it_is_initializable()
  {
      $this->beConstructedWith('some_error_key');
      $this->shouldHaveType(Exception::class);
  }

  function it_should_return_correct_key()
  {
    $this->beConstructedWith('some_error_key');
    $this->getViolation()->errorKey()->shouldReturn('some_error_key');
  }
}

<?php

namespace %namespace%;

use %subject%;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class %name% extends ObjectBehavior
{
  function it_is_initializable()
  {
    $this->shouldHaveType(%subject_class%::class);
  }
}

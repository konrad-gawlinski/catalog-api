<?php

namespace spec\Kwg\Filter\Mapper;

use Kwg\Filter\Mapper\Yaml;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class YamlSpec extends ObjectBehavior
{
    private $fixtures = __DIR__ .'/../../fixtures';
    function it_is_initializable()
    {
        $this->beConstructedWith('file_path.yml');
        $this->shouldHaveType(Yaml::class);
    }

    function it_should_normalize()
    {
        $this->beConstructedWith($this->fixtures .'/simple.yml');
        var_dump($this->normalize()->getWrappedObject());
    }
}

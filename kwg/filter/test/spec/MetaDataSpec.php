<?php

namespace spec\Kwg\Filter;

use Kwg\Filter\MetaData;
use PhpSpec\ObjectBehavior;

class MetaDataSpec extends ObjectBehavior
{
  function it_is_initializable()
  {
    $this->beConstructedWith('property', []);
    $this->shouldHaveType(MetaData::class);
  }

  function it_should_be_created_with_filters()
  {
    $this->beConstructedWith('property', [['trim'], ['ltrim']]);
    $this->getFilters()->shouldReturn([['trim'], ['ltrim']]);
  }

  function it_should_add_one_filter()
  {
    $this->beConstructedWith('property');
    $this->addFilter(['trim']);
    $this->getFilters()->shouldReturn([['trim']]);
  }

  function it_should_add_two_filters()
  {
    $this->beConstructedWith('property');
    $this->addFilter(['trim']);
    $this->addFilter(['ltrim']);
    $this->getFilters()->shouldReturn([['trim'], ['ltrim']]);
  }

  function it_should_throw_exception_given_no_filter()
  {
    $this->beConstructedWith('property');
    $this->shouldThrow('Kwg\Filter\Exception\NoFilter')->during('addFilter', [[]]);
  }

  function it_should_add_one_child()
  {
    $this->beConstructedWith('property');
    $this->addChild(new MetaData('prop'));

    $children = $this->getChildren();

    $children[0]->getPropertyName()->shouldReturn('prop');
  }

  function it_should_add_two_children()
  {
    $this->beConstructedWith('property');
    $this->addChild(new MetaData('prop1'));
    $this->addChild(new MetaData('prop2'));

    $children = $this->getChildren();

    $children[0]->getPropertyName()->shouldReturn('prop1');
    $children[1]->getPropertyName()->shouldReturn('prop2');
  }

}

<?php
namespace spec\Database\Nu3\Core\Database\Gateway;

use Nu3\Spec\App;
use Nu3\Spec\DatabaseHelper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProductSpec extends ObjectBehavior
{
  use DatabaseHelper;

  function let()
  {
    $this->dbconn = App::getInstance()->connectDb()->con();
    $this->beConstructedWith($this->dbconn);
  }

  function it_should_create_product()
  {
    $this->startTransaction();

    $this->endTransaction();
  }
}

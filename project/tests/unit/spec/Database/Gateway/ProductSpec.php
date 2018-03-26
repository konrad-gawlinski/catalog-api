<?php
namespace spec\Database\Nu3\Core\Database\Gateway;

use Nu3\Spec\App;
use Nu3\Spec\DatabaseHelper;
use Nu3\Core\Database;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProductSpec extends ObjectBehavior
{
  use DatabaseHelper;

  function let()
  {
    $this->dbConnection = App::getInstance()->connectDb();
    $this->beConstructedWith($this->dbConnection);
  }

  function it_should_create_product()
  {
    $this->startTransaction();
    $this->create_product('sku_123', 'simple', [])->shouldBeNumeric();
    $this->rollbackTransaction();
  }

  function it_should_fail_create_product_given_not_existing_column()
  {
    $this->startTransaction();

    $this->shouldThrow(Database\Exception::class)->during(
     'create_product',
      ['sku_123', 'simple', ['foo'=>'bar']]
    );

    $this->rollbackTransaction();
  }

}

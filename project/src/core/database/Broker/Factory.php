<?php

namespace Nu3\Core\Database\Broker;

use Nu3\Property\App;

class Factory
{
  use App;

  function getProductBroker() : Product
  {
    return $this->app()['database.product'];
  }
}
<?php

namespace Nu3\Core\Database\Controller;

use Nu3\Property\App;

class Factory
{
  use App;

  function getProductController() : Product
  {
    return $this->app()['database.product'];
  }
}
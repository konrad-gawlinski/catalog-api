<?php

namespace Nu3\Core\Database\Gateway;

use Nu3\Core\Database\Connection;

class Base
{
  /**
   * @var Connection
   */
  protected $dbConnection;

  function __construct(Connection $dbConnection)
  {
    $this->dbConnection = $dbConnection;
  }
}

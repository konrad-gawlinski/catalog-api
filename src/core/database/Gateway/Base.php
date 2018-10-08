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

  function startTransaction()
  {
    $this->dbConnection->startTransaction();
  }

  function commitTransaction()
  {
    $this->dbConnection->commitTransaction();
  }

  function rollbackTransaction()
  {
    $this->dbConnection->rollbackTransaction();
  }
}

<?php

namespace Nu3\Core\Database\Gateway;

use Nu3\Core\Database\Connection;
use Nu3\Core\Database\Exception as DatabaseException;

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

  /**
   * @throws DatabaseException
   */
  protected function runQueryFunction(callable $queryFunction, string $errorMsg)
  {
    try {
      return $queryFunction();
    } catch(\Exception $e) {
      $lastError = pg_last_error($this->dbConnection->connectionRes());
      throw new DatabaseException("{$errorMsg}: ". $lastError, 0, $e);
    }
  }
}

<?php

namespace Nu3\Core\Database;

use Nu3\Core\Database\Exception as DatabaseException;

trait QueryRunner
{
  /**
   * @throws DatabaseException
   */
  private function runQueryFunction(callable $queryFunction, string $errorMsg)
  {
    try {
      return $queryFunction();
    } catch(\Exception $e) {
      $lastError = pg_last_error($this->dbConnection->connectionRes());
      throw new DatabaseException("{$errorMsg}: ". $lastError, 0, $e);
    }
  }
}
<?php

namespace Nu3\Spec;

use Nu3\Core\Database\Connection;

trait DatabaseHelper
{
  /** @var Connection */
  private $dbConnection;

  private function startTransaction()
  {
    pg_query($this->dbConnection->connectionRes(), 'START TRANSACTION');
  }

  private function rollbackTransaction()
  {
    pg_query($this->dbConnection->connectionRes(), 'ROLLBACK');
  }
}
<?php

namespace Nu3\Spec;

trait DatabaseHelper
{
  private $dbconn;

  private function startTransaction()
  {
    pg_query($this->dbconn, 'START TRANSACTION');
  }

  private function endTransaction()
  {
    pg_query($this->dbconn, 'ROLLBACK');
  }
}
<?php
namespace Nu3\Feature;

use Nu3\Core\Database\Connection;

trait DatabaseConnection
{
  private $con = [];

  function setDatabaseConnection(Connection $con)
  {
    $this->con = $con;
  }

  protected function databaseConnection() : Connection
  {
    return $this->con;
  }
}
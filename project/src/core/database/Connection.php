<?php

namespace Nu3\Core\Database;

class Connection
{
  private $connection = null;

  function connect(string $host, string $dbname, string $user, string $password)
  {
    $this->connection = pg_connect("host={$host} dbname={$dbname} user={$user} password={$password} connect_timeout=1");

    if (!$this->connection)
      throw new Exception('Could not connect: ' . pg_last_error());

    return $this->connection;
  }

  function setSearchPath(string $searchPath)
  {
    $result = pg_query($this->connection, "SELECT set_config('search_path', '{$searchPath}', false);");

    if ($result === false)
       throw new Exception("Could not set search_path config '{$searchPath}': " . pg_last_error());
  }

  function connectionRes()
  {
    return $this->connection;
  }

  function startTransaction()
  {
    pg_query($this->connection, "START TRANSACTION");
  }

  function commitTransaction()
  {
    pg_query($this->connection, "COMMIT");
  }

  function rollbackTransaction()
  {
    pg_query($this->connection, "ROLLBACK");
  }
}

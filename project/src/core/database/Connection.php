<?php

namespace Nu3\Core\Database;

class Connection
{
  const SCHEMA_CATALOG = 'catalog';

  private $dbconn = null;

  function connect(string $localhost, string $dbname, string $user, string $password)
  {
    $this->dbconn = pg_connect("host={$localhost} dbname={$dbname} user={$user} password={$password}")
    or die('Could not connect: ' . pg_last_error());
  }

  function disconnect()
  {
    if ($this->dbconn) pg_close($this->dbconn);
  }

  function db()
  {
    return $this->dbconn;
  }
}
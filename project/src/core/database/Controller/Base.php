<?php

namespace Nu3\Core\Database\Controller;

use Nu3\Core\Database\Connection;

class Base
{
  /**
   * @var Connection
   */
  protected $dbconn;

  function __construct(Connection $dbconn)
  {
    $this->dbconn = $dbconn;
  }

  function set_schema(string $schema)
  {
    pg_query_params($this->dbconn->db(), 'SELECT public.set_search_path($1);', [$schema]);
  }

  function disconnect()
  {
    $this->dbconn->disconnect();
  }
}
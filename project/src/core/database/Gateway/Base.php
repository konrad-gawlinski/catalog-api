<?php

namespace Nu3\Core\Database\Gateway;

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

  function setSchemaByStorage(string $storage)
  {
    $map = [
      'catalog_de' => Connection::SCHEMA_CATALOG_DE,
      'catalog_at' => Connection::SCHEMA_CATALOG_AT
    ];

    if (isset($map[$storage])) $this->set_schema($map[$storage]);
    else $this->set_schema(Connection::SCHEMA_CATALOG);
  }

  function disconnect()
  {
    $this->dbconn->disconnect();
  }
}
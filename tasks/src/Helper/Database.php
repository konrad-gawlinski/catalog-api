<?php

namespace Nu3\Task\Helper;

class Database
{
  private $connectionConfig = [];

  function __construct(array $connectionConfig)
  {
    $this->connectionConfig = [
      'host' => $connectionConfig['host'],
      'port' => $connectionConfig['port'],
      'database' => $connectionConfig['database'],
      'user' => $connectionConfig['user'],
      'password' => $connectionConfig['password'],
      'schema' => $connectionConfig['schema']
    ];
  }

  function getSchema() : string
  {
    return $this->connectionConfig['schema'];
  }

  function getConnectionConfig() : array
  {
    return $this->connectionConfig;
  }

  function validatePsqlExec() : bool
  {
    $psql = getenv('PG_BIN');
    if (empty($psql)) {
      echo ("\n PG_BIN env variable not defined. It should provide path to psql executable\n");
      return false;
    }

    return true;
  }
}
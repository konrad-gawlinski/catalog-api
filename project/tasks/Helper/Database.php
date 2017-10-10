<?php

namespace Nu3\Task\Helper;

class Database
{
  function buildConnectionConfig(array $options) : array
  {
    $o = $options;

    return [
      'host' => $o[OPTS_HOST],
      'port' => $o[OPTS_PORT],
      'database' => $o[OPTS_DB],
      'user' => $o[OPTS_USER],
      'password' => $o[OPTS_PASSWORD],
    ];
  }

  function validatePsqlExec() : bool
  {
    $psql = getenv(ENV_PGBIN);
    if (empty($psql)) {
      echo ("\n'". ENV_PGBIN ."' env variable not defined. It should provide path to psql executable\n");
      return false;
    }

    return true;
  }
}
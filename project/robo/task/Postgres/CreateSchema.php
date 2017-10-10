<?php

namespace Nu3\Robo\Task\Postgres;


use Robo\Result;
use Robo\State\Data;

class CreateSchema extends \Robo\Task\BaseTask implements
  \Robo\State\Consumer,
  \Robo\Contract\RollbackInterface
{
  /** @var resource */
  private $con;
  private $schema;

  /**
   * @param string $schema
   */
  function __construct(string $schema)
  {
    $this->schema = $schema;
  }

  /**
   * @param resource $connection
   */
  function connection($connection)
  {
    $this->con = $connection;
  }

  function run() : Result
  {
    $schema = $this->schema;
    $this->printTaskInfo('Creating schema {schema}', [
      'schema' => $schema,
    ]);

    $result = pg_query($this->con, "CREATE SCHEMA {$schema};");

    if ($result === false) {
      return Result::error($this, "Could not create schema '{$schema}': " . pg_last_error());
    }

    return Result::success($this, "Schema '{$schema}' created");
  }
  
  function receiveState(Data $state)
  {
    $this->connection($state['connection']);
  }

  function rollback()
  {
    $schema = $this->schema;
    $result = pg_query($this->con, "DROP SCHEMA {$schema};");
    
    if ($result === false) {
      $this->printTaskWarning('During rollback schema [{schema}]could not be dropped', ['schema' => $schema]);
    }

    $this->printTaskInfo('ROLLBACK: removed schema [{schema}]', ['schema' => $schema]);
  }
}
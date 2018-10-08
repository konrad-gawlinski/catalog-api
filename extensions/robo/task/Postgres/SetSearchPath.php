<?php

namespace Nu3\Robo\Task\Postgres;


use Robo\Result;
use Robo\State\Data;

class SetSearchPath extends \Robo\Task\BaseTask implements
  \Robo\State\Consumer
{
  /** @var resource */
  private $con;
  private $searchPath;

  /**
   * @param string $searchPath
   */
  function __construct(string $searchPath)
  {
    $this->searchPath = $searchPath;
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
    $searchPath = $this->searchPath;
    $this->printTaskInfo('Setting search path {path}', [
      'path' => $searchPath,
    ]);
    $result = pg_query($this->con, "SELECT set_config('search_path', '{$searchPath}', false);");

    if ($result === false)
      return Result::error($this, "Could not set search_path config '{$searchPath}': " . pg_last_error());

    return Result::success($this, "Schema '{$searchPath}' created");
  }
  
  function receiveState(Data $state)
  {
    $this->connection($state['connection']);
  }
}
<?php

namespace Nu3\Robo\Task\Postgres;

use Robo\Result;

class Connect extends \Robo\Task\BaseTask
{
  private $host;
  private $port;
  private $database;
  private $user;
  private $password;
  private $conTimeout = 1;

  function __construct(array $config)
  {
    $this->host = $config['host'] ?? 'localhost';
    $this->port = $config['port'] ?? '5432';
    $this->database = $config['database'];
    $this->user = $config['user'];
    $this->password = $config['password'];
  }

  function timeout(int $seconds)
  {
    $this->conTimeout = $seconds;
  }

  function run()
  {
    $this->printTaskInfo('Connecting to host={host}, port={port}, database={database}', [
      'host' => $this->host,
      'port' => $this->port,
      'database' => $this->database,
    ]);

    $con = pg_connect(
      "host={$this->host} port={$this->port} dbname={$this->database} user={$this->user} password={$this->password} connect_timeout={$this->conTimeout}"
    );

    $data = ['connection' => $con];

    if ($con) {
      return Result::success($this, 'Connection success', $data);
    }

    return Result::error($this, 'Could not connect: ' . pg_last_error(), $data);
  }
}
<?php

namespace Nu3\Robo\Task\Postgres;

use Robo\Result;
use Robo\Common\BuilderAwareTrait;
use Robo\Contract\BuilderAwareInterface;

class ImportSql extends \Robo\Task\BaseTask implements
  BuilderAwareInterface
{
  use BuilderAwareTrait;

  private $filePath;
  private $host;
  private $port;
  private $database;
  private $user;
  private $password;

  private $psqlExec = 'psql';

  function __construct(array $config, string $filePath)
  {
    $this->host = $config['host'] ?? 'localhost';
    $this->port = $config['port'] ?? '5432';
    $this->database = $config['database'];
    $this->user = $config['user'];
    $this->password = $config['password'];
    $this->filePath = $filePath;
  }

  function psqlExecPath(string $execPath)
  {
    $this->psqlExec = $execPath;
    return $this;
  }

  function run() : Result
  {
    $filePath = $this->filePath;
    if (!file_exists($filePath)) {
      return Result::error($this, "Could not read file '{$filePath}'");
    }

    if ($this->importFile($filePath)) {
      return Result::success($this, "File '{$filePath}' import success");
    }

    return Result::error($this, "Could not import file '{$filePath}'");
  }

  private function importFile(string $filePath)
  {
    $dbCSHost = "{$this->host}:{$this->port}/{$this->database}?connect_timeout=1";
    $connectionString = "postgresql://{$this->user}:{$this->password}@{$dbCSHost}";
    $protectedConnectionString = "postgresql://xxx:xxx@{$dbCSHost}";

    $this->printTaskInfo('Importing file {file}', ['file' => $filePath]);
    $this->printTaskInfo('Connection string: {cs}', ['cs' => $protectedConnectionString]);

    /** @var Result $result */
    $result = $this->collectionBuilder()
      ->taskExec($this->psqlExec)
      ->silent(true)
      ->rawArg($connectionString . " < {$filePath}")
      ->run();

    if ($result->wasSuccessful()) {
      return true;
    }

    return false;
  }
}

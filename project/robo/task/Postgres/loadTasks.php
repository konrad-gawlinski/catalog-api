<?php

namespace Nu3\Robo\Task\Postgres;

trait loadTasks
{

  protected function taskPostgresConnect(array $config)
  {
    return $this->task(Connect::class, $config);
  }

  protected function taskCreateSchema(string $schema)
  {
    return $this->task(CreateSchema::class, $schema);
  }
  
  protected function taskImportSqlFile(array $config, string $filePath)
  {
    return $this->task(ImportSql::class, $config, $filePath);
  }

  protected function taskSetSearchPath(string $searchPath)
  {
    return $this->task(SetSearchPath::class, $searchPath);
  }
}

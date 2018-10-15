<?php

use Nu3\Config;

define('APPLICATION_ROOT', __DIR__ . '/../../');

require_once APPLICATION_ROOT . 'vendor/autoload.php';
require __DIR__ . '/../src/Helper/Database.php';

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
  use Nu3\Robo\Task\Postgres\loadTasks;

  /** @var \Nu3\Task\Helper\Database */
  private $dbHelper;

  private const TMP_DIR = '/tmp/';

  function __construct()
  {
    $app = require_once APPLICATION_ROOT .'src/bootstrap.php';
    $dbConfig = $app['config'][Config::DB];
    $this->dbHelper = new Nu3\Task\Helper\Database([
      'host' => $dbConfig[Config::DB_HOST],
      'port' => $dbConfig[Config::DB_PORT],
      'database' => $dbConfig[Config::DB_NAME],
      'user' => $dbConfig[Config::DB_USER],
      'password' => $dbConfig[Config::DB_PASS],
      'schema' => $dbConfig[Config::DB_SCHEMA]
    ]);
  }

  function databaseInit() : bool
  {
    if (!$this->dbHelper->validatePsqlExec()) {
      return false;
    }

    $inputFile = APPLICATION_ROOT . 'database/init.sql';
    $fileToImport = self::TMP_DIR . 'database_init.sql';
    $this->prepareFile($inputFile, $fileToImport);

    return $this->importSqlFile($fileToImport);
  }

  private function prepareFile(string $inputFile, string $outputFile) : bool
  {
    $collection = $this->collectionBuilder();
    $collection
      ->taskFilesystemStack()
      ->copy($inputFile, $outputFile, true)
      ->taskReplaceInFile($outputFile)
      ->from('<schema_name>')
      ->to($this->dbHelper->getSchema());

    $result = $collection->run();
    if ($result->wasSuccessful()) return true;

    return false;
  }

  private function importSqlFile(string $filePath) : bool
  {
    $connectionConfig = $this->dbHelper->getConnectionConfig();
    $result = $this->collectionBuilder()
      ->taskImportSqlFile($connectionConfig, $filePath)
      ->psqlExecPath(getenv('PG_BIN'))
      ->run();

    if ($result->wasSuccessful()) return true;

    return false;
  }
}

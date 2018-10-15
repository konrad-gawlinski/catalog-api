<?php

use \Robo\Collection\CollectionBuilder;
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

  private const TMP_DIR = '/tmp/';

  /** @var \Nu3\Task\Helper\Database */
  private $dbHelper;

  public function __construct()
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

  function productCreateTables() : bool
  {
    return $this->handleTask(function() {
      $inputFile = APPLICATION_ROOT . 'database/product/01_init.sql';
      $fileToImport = self::TMP_DIR . 'product_tables.sql';

      return $this->runCreateCollection($inputFile, $fileToImport);
    });
  }

  function productDropTables() : bool
  {
    return $this->handleTask(function() {
      $inputFile = APPLICATION_ROOT . 'database/product/01_cleanup.sql';
      $fileToImport = self::TMP_DIR . 'product_drop_tables.sql';

      return $this->runDropCollection($inputFile, $fileToImport);
    });
  }

  private function handleTask(callable $callback) : bool
  {
    if (!$this->dbHelper->validatePsqlExec()) {
      return false;
    }

    $result = $callback();
    if ($result->wasSuccessful()) return true;

    return false;
  }

  private function runCreateCollection(string $inputFile, string $outputFile) : \Robo\Result
  {
    $this->stopOnFail(true);
    $connectionConfig = $this->dbHelper->getConnectionConfig();

    $collection = $this->collectionBuilder();
    $this->addCloneAndReplaceInFileTasks($collection, $inputFile, $outputFile);
    $this->addImportSqlTask($collection, $connectionConfig, $outputFile);

    return $collection->run();
  }

  private function runDropCollection(string $inputFile, string $outputFile) : \Robo\Result
  {
    $this->stopOnFail(true);
    $connectionConfig = $this->dbHelper->getConnectionConfig();

    $collection = $this->collectionBuilder();
    $this->addCloneAndReplaceInFileTasks($collection, $inputFile, $outputFile);
    $this->addImportSqlTask($collection, $connectionConfig, $outputFile);

    return $collection->run();
  }

  private function addCloneAndReplaceInFileTasks(
    CollectionBuilder $collection, string $inputFilePath, string $outputFilePath
  )
  {
    $collection
      ->taskFilesystemStack()
        ->copy($inputFilePath, $outputFilePath, true)
      ->taskReplaceInFile($outputFilePath)
        ->from('<schema_name>')
        ->to($this->dbHelper->getSchema());
  }

  private function addImportSqlTask(
    CollectionBuilder $collection, array $connectionConfig, string $filePath
  )
  {
    $collection->taskPostgresConnect($connectionConfig)
      ->taskImportSqlFile($connectionConfig, $filePath)
      ->psqlExecPath(getenv('PG_BIN'));
  }
}

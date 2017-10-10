<?php

define('APPLICATION_ROOT', __DIR__ . '/../..');

define('OPTS_HOST', 'host');
define('OPTS_PORT', 'port');
define('OPTS_DB', 'database');
define('OPTS_USER', 'user');
define('OPTS_PASSWORD', 'password');
define('OPTS_SCHEMA', 'schema');
define('OPTS_SEARCH_PATH', 'search_path');

define ('ENV_PGBIN', 'PG_BIN');

require_once APPLICATION_ROOT . '/vendor/autoload.php';
require __DIR__ .'/../Helper/Database.php';

use \Robo\Collection\CollectionBuilder;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
  use Nu3\Robo\Task\Postgres\loadTasks;

  private const TMP_DIR = '/tmp';

  /** @var \Nu3\Task\Helper\Database */
  private $dbHelper;

  public function __construct()
  {
    $this->dbHelper = new Nu3\Task\Helper\Database();

    \Robo\Robo::loadConfiguration([__DIR__ . '/robo.yml']);
  }

  /**
   * Create schema and define database tables
   *
   * @param array $options
   * @option $host database host to connect to
   * @option $port database port to connect to
   * @option $database database name to connect to
   * @option $user database user
   * @option $password database password
   * @option $schema schema name to create for storing tables
   * @option $search_path
   */
  function productCreateTables($options = [
    OPTS_HOST => 'localhost',
    OPTS_PORT => 5432,
    OPTS_DB => null,
    OPTS_USER => null,
    OPTS_PASSWORD => null,
    OPTS_SCHEMA => null,
    OPTS_SEARCH_PATH => null,
  ]) : bool
  {
    return $this->handleTask($options, function($options) {
      $inputFile = APPLICATION_ROOT . '/database/product/01_tables.sql';
      $fileToImport = self::TMP_DIR . '/product_tables.sql';

      return $this->runCreateCollection($options, $inputFile, $fileToImport);
    });
  }

  /**
   * Create schema and define database tables
   *
   * @param array $options
   * @option $host database host to connect to
   * @option $port database port to connect to
   * @option $database database name to connect to
   * @option $user database user
   * @option $password database password
   * @option $schema schema name to create for storing stored procedures
   * @option $search_path
   */
  function productCreateProcedures($options = [
    OPTS_HOST => 'localhost',
    OPTS_PORT => 5432,
    OPTS_DB => null,
    OPTS_USER => null,
    OPTS_PASSWORD => null,
    OPTS_SCHEMA => null,
    OPTS_SEARCH_PATH => null,
  ]) : bool
  {
    return $this->handleTask($options, function($options) {
      $inputFile = APPLICATION_ROOT . '/database/product/02_procedures.sql';
      $fileToImport = self::TMP_DIR . '/product_procedures.sql';

      return $this->runCreateCollection($options, $inputFile, $fileToImport);
    });
  }

  /**
   * Create schema and define database tables
   *
   * @param array $options
   * @option $host database host to connect to
   * @option $port database port to connect to
   * @option $database database name to connect to
   * @option $user database user
   * @option $password database password
   * @option $schema schema name to remove
   */
  function productDropProcedures($options = [
    OPTS_HOST => 'localhost',
    OPTS_PORT => 5432,
    OPTS_DB => null,
    OPTS_USER => null,
    OPTS_PASSWORD => null,
    OPTS_SCHEMA => null
  ]) : bool
  {
    return $this->handleTask($options, function($options) {
      $inputFile = APPLICATION_ROOT . '/database/product/cleanup/01_procedures.sql';
      $fileToImport = self::TMP_DIR . '/product_drop_procedures.sql';

      return $this->runDropCollection($options, $inputFile, $fileToImport);
    });
  }

  /**
   * Create schema and define database tables
   *
   * @param array $options
   * @option $host database host to connect to
   * @option $port database port to connect to
   * @option $database database name to connect to
   * @option $user database user
   * @option $password database password
   * @option $schema schema name to remove
   */
  function productDropTables($options = [
    OPTS_HOST => 'localhost',
    OPTS_PORT => 5432,
    OPTS_DB => null,
    OPTS_USER => null,
    OPTS_PASSWORD => null,
    OPTS_SCHEMA => null
  ]) : bool
  {
    return $this->handleTask($options, function($options) {
      $inputFile = APPLICATION_ROOT . '/database/product/cleanup/02_tables.sql';
      $fileToImport = self::TMP_DIR . '/product_drop_tables.sql';

      return $this->runDropCollection($options, $inputFile, $fileToImport);
    });
  }

  private function handleTask(array $options, callable $callback) : bool
  {
    if (!$this->dbHelper->validatePsqlExec()) {
      return false;
    }

    $result = $callback($options);
    if ($result->wasSuccessful()) return true;

    return false;
  }

  private function runCreateCollection(array $options, string $inputFile, string $outputFile) : \Robo\Result
  {
    $this->stopOnFail(true);
    $connectionConfig = $this->dbHelper->buildConnectionConfig($options);

    $collection = $this->collectionBuilder();
    $this->addCloneAndReplaceInFileTasks($collection, $inputFile, $outputFile, $options[OPTS_SCHEMA]);
    $this->addCreateSchemaAndImportSqlTask($collection, $connectionConfig, $outputFile, $options);

    return $collection->run();
  }

  private function runDropCollection(array $options, string $inputFile, string $outputFile) : \Robo\Result
  {
    $this->stopOnFail(true);
    $connectionConfig = $this->dbHelper->buildConnectionConfig($options);

    $collection = $this->collectionBuilder();
    $this->addCloneAndReplaceInFileTasks($collection, $inputFile, $outputFile, $options[OPTS_SCHEMA]);
    $this->addImportSqlTask($collection, $connectionConfig, $outputFile);

    return $collection->run();
  }

  private function addCloneAndReplaceInFileTasks(
    CollectionBuilder $collection, string $inputFilePath, string $outputFilePath, string $schema
  )
  {
    $collection
      ->taskFilesystemStack()
        ->copy($inputFilePath, $outputFilePath, true)
      ->taskReplaceInFile($outputFilePath)
        ->from('<schema_name>')
        ->to($schema);
  }

  private function addCreateSchemaAndImportSqlTask(
    CollectionBuilder $collection, array $connectionConfig, string $filePath, array $options
  )
  {
    $collection->taskReplaceInFile($filePath)
      ->from('<search_path>')
      ->to($options[OPTS_SEARCH_PATH]);

    $collection->taskPostgresConnect($connectionConfig)
      ->taskCreateSchema($options[OPTS_SCHEMA])
      ->taskImportSqlFile($connectionConfig, $filePath)
      ->psqlExecPath(getenv(ENV_PGBIN));
  }

  private function addImportSqlTask(
    CollectionBuilder $collection, array $connectionConfig, string $filePath
  )
  {
    $collection->taskPostgresConnect($connectionConfig)
      ->taskImportSqlFile($connectionConfig, $filePath)
      ->psqlExecPath(getenv(ENV_PGBIN));
  }
}

<?php

define('APPLICATION_ROOT', __DIR__ . '/../..');

define('OPTS_HOST', 'host');
define('OPTS_PORT', 'port');
define('OPTS_DB', 'database');
define('OPTS_USER', 'user');
define('OPTS_PASSWORD', 'password');
define('OPTS_SCHEMA', 'schema');

define ('ENV_PGBIN', 'PG_BIN');

require_once APPLICATION_ROOT . '/vendor/autoload.php';

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

  public function __construct()
  {
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
   */
  function productCreateTables($options = [
    OPTS_HOST => 'localhost',
    OPTS_PORT => 5432,
    OPTS_DB => null,
    OPTS_USER => null,
    OPTS_PASSWORD => null,
    OPTS_SCHEMA => null
  ]) : bool
  {
    if (!$this->validatePsqlExec()) {
      return false;
    }

    $inputFile = APPLICATION_ROOT . '/database/product/01_tables.sql';
    $fileToImport = self::TMP_DIR . '/product_tables.sql';
    $result = $this->runCreateCollection($options, $inputFile, $fileToImport);

    if ($result->wasSuccessful()) return true;

    return false;
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
   */
  function productCreateProcedures($options = [
    OPTS_HOST => 'localhost',
    OPTS_PORT => 5432,
    OPTS_DB => null,
    OPTS_USER => null,
    OPTS_PASSWORD => null,
    OPTS_SCHEMA => null
  ]) : bool
  {
    if (!$this->validatePsqlExec()) {
      return false;
    }

    $inputFile = APPLICATION_ROOT . '/database/product/02_procedures.sql';
    $fileToImport = self::TMP_DIR . '/product_procedures.sql';
    $result = $this->runCreateCollection($options, $inputFile, $fileToImport);

    if ($result->wasSuccessful()) return true;

    return false;
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
    if (!$this->validatePsqlExec()) {
      return false;
    }

    $inputFile = APPLICATION_ROOT . '/database/product/cleanup/01_procedures.sql';
    $fileToImport = self::TMP_DIR . '/product_drop_procedures.sql';
    $result = $this->runDropCollection($options, $inputFile, $fileToImport);

    if ($result->wasSuccessful()) return true;

    return false;
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
    if (!$this->validatePsqlExec()) {
      return false;
    }

    $inputFile = APPLICATION_ROOT . '/database/product/cleanup/02_tables.sql';
    $fileToImport = self::TMP_DIR . '/product_drop_tables.sql';
    $result = $this->runDropCollection($options, $inputFile, $fileToImport);

    if ($result->wasSuccessful()) return true;

    return false;
  }
  
  private function validatePsqlExec() : bool
  {
    $psql = getenv(ENV_PGBIN);
    if (empty($psql)) {
      $this->say("'". ENV_PGBIN ."' env variable not defined. It should provide path to psql executable");
      return false;
    }

    return true;
  }

  private function runCreateCollection(array $options, string $inputFile, string $outputFile) : \Robo\Result
  {
    $this->stopOnFail(true);
    $o = $options;
    $connectionConfig = [
      'host' => $o[OPTS_HOST],
      'port' => $o[OPTS_PORT],
      'database' => $o[OPTS_DB],
      'user' => $o[OPTS_USER],
      'password' => $o[OPTS_PASSWORD],
    ];

    $collection = $this->collectionBuilder();
    $this->addCloneAndReplaceInFileTasks($collection, $inputFile, $outputFile, $o[OPTS_SCHEMA]);
    $this->addCreateSchemaAndImportSqlTask($collection, $connectionConfig, $outputFile, $o[OPTS_SCHEMA]);

    return $collection->run();
  }

  private function runDropCollection(array $options, string $inputFile, string $outputFile) : \Robo\Result
  {
    $this->stopOnFail(true);
    $o = $options;
    $connectionConfig = [
      'host' => $o[OPTS_HOST],
      'port' => $o[OPTS_PORT],
      'database' => $o[OPTS_DB],
      'user' => $o[OPTS_USER],
      'password' => $o[OPTS_PASSWORD],
    ];

    $collection = $this->collectionBuilder();
    $this->addCloneAndReplaceInFileTasks($collection, $inputFile, $outputFile, $o[OPTS_SCHEMA]);
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
    CollectionBuilder $collection, array $connectionConfig, string $filePath, string $schema
  )
  {
    $collection->taskPostgresConnect($connectionConfig)
      ->taskCreateSchema($schema)
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

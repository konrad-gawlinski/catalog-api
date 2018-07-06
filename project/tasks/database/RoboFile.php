<?php

define('APPLICATION_ROOT', __DIR__ . '/../..');

define('OPTS_HOST', 'host');
define('OPTS_PORT', 'port');
define('OPTS_DB', 'database');
define('OPTS_USER', 'user');
define('OPTS_PASSWORD', 'password');

define ('ENV_PGBIN', 'PG_BIN');

require_once APPLICATION_ROOT . '/vendor/autoload.php';
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

  function __construct()
  {
    $this->dbHelper = new Nu3\Task\Helper\Database();
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
   */
  function databaseCreate($options = [
    OPTS_HOST => 'localhost',
    OPTS_PORT => 5432,
    OPTS_DB => null,
    OPTS_USER => null,
    OPTS_PASSWORD => null,
  ]) : bool
  {
    $inputFile = APPLICATION_ROOT . '/database/01_create.sql';

    return $this->importSqlFile($options, $inputFile);
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
   */
  function databaseInit($options = [
    OPTS_HOST => 'localhost',
    OPTS_PORT => 5432,
    OPTS_DB => null,
    OPTS_USER => null,
    OPTS_PASSWORD => null,
  ]) : bool
  {
    $inputFile = APPLICATION_ROOT . '/database/02_init.sql';

    return $this->importSqlFile($options, $inputFile);
  }

  private function importSqlFile(array $options, string $filePath) : bool
  {
    if (!$this->dbHelper->validatePsqlExec()) {
      return false;
    }

    $connectionConfig = $this->dbHelper->buildConnectionConfig($options);
    $result = $this->collectionBuilder()
      ->taskImportSqlFile($connectionConfig, $filePath)
      ->psqlExecPath(getenv(ENV_PGBIN))
      ->run();

    if ($result->wasSuccessful()) return true;

    return false;
  }
}

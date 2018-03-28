<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class DatabaseTester extends \Codeception\Actor
{
  use _generated\DatabaseTesterActions;

  private static $app;

  /** @var  \Nu3\Core\Database\Connection */
  private static $dbConnection;

  function __construct(\Codeception\Scenario $scenario)
  {
    parent::__construct($scenario);

    if (!self::$app)
      self::$app = require __DIR__ . '/../../../src/bootstrap.php';
  }

  function app()
  {
    return self::$app;
  }

  function dbConnection()
  {
    if (!self::$dbConnection) {
      self::$dbConnection = self::$app['database.connection'];
    }

    return self::$dbConnection;
  }

  function startTransaction()
  {
    pg_query($this->dbConnection()->connectionRes(), 'START TRANSACTION');
  }

  function rollbackTransaction()
  {
    pg_query($this->dbConnection()->connectionRes(), 'ROLLBACK');
  }

  function rethrowExceptionInsideTransaction(callable $codeToRun)
  {
    $this->startTransaction();

    try {
      $codeToRun();
    } catch(Exception $e) {
      $this->rollbackTransaction();
      throw $e;
    }

    $this->rollbackTransaction();
  }

  function assertQueryResult(string $assertQuery, string $expectedQuery) : int
  {
    $query = <<<QUERY
WITH assert_query AS (
  ({$assertQuery}) INTERSECT ALL ({$expectedQuery})
) SELECT count(1) FROM assert_query
QUERY;
    
    $result = pg_query($this->dbConnection()->connectionRes(), $query);
    return pg_fetch_row($result)[0];
  }

   /**
  * Define custom actions here
  */
}

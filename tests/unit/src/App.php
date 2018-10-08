<?php

namespace Nu3\Spec;

use Nu3\Core\Database\Connection as DatabaseConnection;

class App
{
  private static $app;
  private static $instance;

  /** @var DatabaseConnection */
  private static $dbConnection;

  private function __construct()
  {
    $this::$app = require __DIR__.'/../../../src/bootstrap.php';
  }

  static function getInstance()
  {
    if (!self::$instance) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  function connectDb() : DatabaseConnection
  {
    if (!self::$dbConnection) {
      self::$dbConnection = $this::$app['database.connection'];
    }

    return self::$dbConnection;
  }

  function getConfig() : array
  {
    return $this::$app['config'];
  }
}

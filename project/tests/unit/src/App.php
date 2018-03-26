<?php

namespace Nu3\Spec;

class App
{
  private static $app;
  private static $instance;

  private static $dbConnection;

  private function __construct() {
    $this::$app = require __DIR__.'/../../../src/bootstrap.php';
  }

  static function getInstance() {
    if (!self::$instance) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  function connectDb() {
    if (!self::$dbConnection) {
      self::$dbConnection = $this::$app['database.connection'];
    }

    return self::$dbConnection;
  }
}
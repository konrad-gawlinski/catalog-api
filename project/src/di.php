<?php

use Nu3\Config;

$app['config'] = function() {
  return require(APPLICATION_ROOT.'config/config.php');
};

$app['database.connection'] = function() use ($app) {
  $config = $app['config'][Config::DB];
  $db = new Nu3\Core\Database\Connection();

  $db->connect(
    $config[Config::DB_HOST],
    $config[Config::DB_NAME],
    $config[Config::DB_USER],
    $config[Config::DB_PASS]
  );

  return $db;
};

$app['database.product'] = function() use ($app) {
  return new Nu3\Core\Database\Broker\Product($app['database.connection']);
};

$app['service.product'] = function() use ($app) {
  $controller = new Nu3\Service\Product\Controller();
  
  return $controller;
};

$app['product.model'] = function() use ($app) {
  $model = new \Nu3\Service\Product\Model();
  $model->setJsonValidator($app['json.validator']);
  $model->setValidator($app['validator']);
  $model->setDbFactory($app['database.factory']);

  return $model;
};

$app['database.factory'] = function() use ($app) {
  $factory = new \Nu3\Core\Database\Broker\Factory();
  $factory->setApp($app);

  return $factory;
};

$app['json.validator'] = function() use ($app) {
  return new \Nu3\Core\JsonValidator(
    new \JsonSchema\Validator()
  );
};

$app['validator'] = function() use ($app) {
  return new \Nu3\Core\Validator();
};

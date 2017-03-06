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

$app['product.gateway'] = function() use ($app) {
  return new Nu3\Core\Database\Gateway\Product($app['database.connection']);
};

$app['product.save_action'] = function() use ($app) {
  $action = new Nu3\Service\Product\SaveAction();
  
  return $action;
};

$app['product.validator.entity'] = function() use ($app) {
  $validator = new \Nu3\Service\Product\EntityValidator();
  $validator->setConfig($app['config']);

  return $validator;
};

$app['product.save_request.validator'] = function() use ($app) {
  $validator = new \Nu3\Service\Product\Request\Validator();
  $validator->setConfig($app['config']);

  return $validator;
};

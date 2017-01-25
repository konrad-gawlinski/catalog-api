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
  return new Nu3\Core\Database\Controller\Product($app['database.connection']);
};

$app['service.product'] = function() use ($app) {
  $controller = new Nu3\Service\Product\Controller();
  
  return $controller;
};

$app['product.model'] = function() use ($app) {
  $model = new \Nu3\Service\Product\Model();
  $model->setPayloadValidator($app['product.validator.payload']);
  $model->setEntityValidator($app['product.validator.entity']);
  $model->setDbFactory($app['database.factory']);

  return $model;
};

$app['database.factory'] = function() use ($app) {
  $factory = new \Nu3\Core\Database\Controller\Factory();
  $factory->setApp($app);

  return $factory;
};

$app['product.validator.payload'] = function() use ($app) {
  return new \Nu3\Service\Product\PayloadValidator();
};

$app['product.validator.entity'] = function() use ($app) {
  return new \Nu3\Service\Product\EntityValidator();
};

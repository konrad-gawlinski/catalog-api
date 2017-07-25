<?php

use Nu3\Config;

$app['config'] = function() {
  return require(APPLICATION_ROOT.'config/config.php');
};

$app['product.service.create.factory'] = function() use ($app) {
  $factory = new \Nu3\Service\Product\Action\CreateProduct\Factory();
  $factory->setConfig($app['config']);
  $factory->setDatabaseConnection($app['database.connection']);

  return $factory;
};

$app['product.service.update.factory'] = function() use ($app) {
  $factory = new \Nu3\Service\Product\Action\UpdateProduct\Factory();
  $factory->setConfig($app['config']);
  $factory->setDatabaseConnection($app['database.connection']);

  return $factory;
};

$app['product.service.get.factory'] = function() use ($app) {
  $factory = new \Nu3\Service\Product\GetAction\Factory();
  $factory->setConfig($app['config']);

  return $factory;
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

$app['product.create_action'] = function() use ($app) {
  return new Nu3\Service\Product\Action\CreateProduct\Action($app['product.service.create.factory']);
};

$app['product.update_action'] = function() use ($app) {
  return new Nu3\Service\Product\Action\UpdateProduct\Action($app['product.service.update.factory']);
};

$app['product.get_action'] = function() use ($app) {
  return new Nu3\Service\Product\GetAction\Action($app['product.service.get.factory']);
};

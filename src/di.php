<?php

use Nu3\Config;
use Nu3\Service\Product\PropertyMap;
use Nu3\Core\RegionUtils;

$app['config'] = function() {
  return require(APPLICATION_ROOT .'config/config.php');
};

$app['product.service.create.factory'] = function() use ($app) {
  $factory = new \Nu3\Service\Product\Action\CreateProduct\Factory();
  $factory->setConfig($app['config']);
  $factory->setDatabaseConnection($app['database.connection']);
  $factory->setPropertyMap(new PropertyMap());
  $factory->setRegionUtils(new RegionUtils());

  return $factory;
};

$app['product.service.update.factory'] = function() use ($app) {
  $factory = new \Nu3\Service\Product\Action\UpdateProduct\Factory();
  $factory->setConfig($app['config']);
  $factory->setDatabaseConnection($app['database.connection']);
  $factory->setPropertyMap(new PropertyMap());
  $factory->setRegionUtils(new RegionUtils());

  return $factory;
};

$app['product.service.get.factory'] = function() use ($app) {
  $factory = new \Nu3\Service\Product\Action\GetProduct\Factory();
  $factory->setConfig($app['config']);
  $factory->setDatabaseConnection($app['database.connection']);
  $factory->setPropertyMap(new PropertyMap());
  $factory->setRegionUtils(new RegionUtils());

  return $factory;
};

$app['database.connection'] = function() use ($app) {
  $config = $app['config'][Config::DB];
  $db = new Nu3\Core\Database\Connection();
  $tokens = parse_url($config[Config::DB_DSN]);

  $db->connect(
    $tokens['host'],
    ltrim($tokens['path'], '/'),
    $tokens['user'],
    $tokens['pass']
  );
  
  $db->setSearchPath("{$config[Config::DB_PROCEDURES_SCHEMA]}, {$config[Config::DB_DATA_SCHEMA]}");

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
  $action =  new Nu3\Service\Product\Action\GetProduct\Action($app['product.service.get.factory']);
  $action->setConfig($app['config']);

  return $action;
};

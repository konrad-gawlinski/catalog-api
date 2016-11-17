<?php

$app['config'] = function() {
  return require(APPLICATION_ROOT.'config/config.php');
};

$app['service.product'] = function() use ($app) {
  $controller = new Nu3\Service\Product\Controller();
  $controller->setConfig($app['config']);
  return $controller;
};

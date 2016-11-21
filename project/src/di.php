<?php

use Symfony\Component\Serializer;

$app['config'] = function() {
  return require(APPLICATION_ROOT.'config/config.php');
};

$app['service.product'] = function() use ($app) {
  $controller = new Nu3\Service\Product\Controller();
  $controller->setConfig($app['config']);
  return $controller;
};

$app['product.serializer'] = function() use ($app) {
  return new \Nu3\Service\Product\Serializer(
    $app['json.serializer'],
    new \JsonSchema\Validator()
  );
};

$app['json.serializer'] = function() {
  return new Serializer\Serializer(
    [new Serializer\Normalizer\PropertyNormalizer()],
    [new Serializer\Encoder\JsonEncoder()]
  );
};

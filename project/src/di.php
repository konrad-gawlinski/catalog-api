<?php

$app['config'] = function() {
  return require(APPLICATION_ROOT.'config/config.php');
};

$app['service.product'] = function() use ($app) {
  $controller = new Nu3\Service\Product\Controller();
  $controller->setConfig($app['config']);
  $controller->setJsonValidator($app['product.json-validator']);
  $controller->setSerializer($app['product.serializer']);
  $controller->setValidator($app['product.validator']);
  
  return $controller;
};

$app['product.json-validator'] = function() use ($app) {
  return new \Nu3\Service\Product\JsonValidator(
    new \JsonSchema\Validator()
  );
};

$app['product.serializer'] = function() use ($app) {
  return new \Nu3\Service\Product\Serializer($app['json.serializer']);
};

$app['product.validator'] = function() use ($app) {
    return new \Nu3\Service\Product\Validator($app['validator.builder']);
};

$app['json.serializer'] = function() {
  return \JMS\Serializer\SerializerBuilder::create()
    ->setCacheDir(APPLICATION_ROOT . 'cache/jms/product')
    ->build();
};

$app['validator.builder'] = function() {
  return \Symfony\Component\Validator\Validation::createValidatorBuilder()
    ->setMetadataCache(
      new \Symfony\Component\Validator\Mapping\Cache\DoctrineCache(
        new \Doctrine\Common\Cache\ArrayCache()
      ));
};

<?php

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

$app['product.validator'] = function() use ($app) {
    return new \Nu3\Service\Product\Validator($app['validator.builder']);
};

$app['json.serializer'] = function() {
  return new Symfony\Component\Serializer\Serializer(
    [new Symfony\Component\Serializer\Normalizer\PropertyNormalizer()],
    [new Symfony\Component\Serializer\Encoder\JsonEncoder()]
  );
};

$app['validator.builder'] = function() {
  return \Symfony\Component\Validator\Validation::createValidatorBuilder()
    ->setMetadataCache(
      new \Symfony\Component\Validator\Mapping\Cache\DoctrineCache(
        new \Doctrine\Common\Cache\ArrayCache()
      ));
};

<?php

$app['config'] = function() {
  return require(APPLICATION_ROOT.'config/config.php');
};

$app['service.product'] = function() use ($app) {
  $controller = new Nu3\Service\Product\Controller();
  $controller->setConfig($app['config']);
  $controller->setJsonValidator($app['product.json-validator']);
  $controller->setSerializer($app['serializer']);
  $controller->setValidator($app['validator']);
  $controller->setSanitizer($app['sanitizer']);
  
  return $controller;
};

$app['product.json-validator'] = function() use ($app) {
  return new \Nu3\Core\JsonValidator(
    new \JsonSchema\Validator()
  );
};

$app['json.serializer'] = function() {
  return \JMS\Serializer\SerializerBuilder::create()
    ->setCacheDir(APPLICATION_ROOT . 'cache/jms/product')
    ->build();
};

$app['serializer'] = function() use ($app) {
  return new \Nu3\Core\Serializer($app['json.serializer']);
};

$app['sanitizer'] = function() {
  $reader = new \Doctrine\Common\Annotations\AnnotationReader();
  $loader = new \DMS\Filter\Mapping\Loader\AnnotationLoader($reader);
  $metadataFactory = new \DMS\Filter\Mapping\ClassMetadataFactory($loader);
  $filterLoader = new \DMS\Filter\Filters\Loader\FilterLoader();

  return new DMS\Filter\Filter($metadataFactory, $filterLoader);
};

$app['validator'] = function() use ($app) {
  return new \Nu3\Core\Validator($app['validator.builder']);
};

$app['validator.builder'] = function() {
  return \Symfony\Component\Validator\Validation::createValidatorBuilder()
    ->enableAnnotationMapping()
    ->setMetadataCache(
      new \Symfony\Component\Validator\Mapping\Cache\DoctrineCache(
        new \Doctrine\Common\Cache\ArrayCache()
      ));
};



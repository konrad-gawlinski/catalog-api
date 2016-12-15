<?php
define('APPLICATION_ROOT', __DIR__.'/../');

$loader = require_once APPLICATION_ROOT.'vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
  'JMS\Serializer\Annotation',
  APPLICATION_ROOT . 'vendor/jms/serializer/src'
);

$app = new Silex\Application();
$app['debug'] = true;

require APPLICATION_ROOT.'src/Config.php';
require APPLICATION_ROOT.'src/di.php';
require APPLICATION_ROOT.'src/routing.php';

$app->run();
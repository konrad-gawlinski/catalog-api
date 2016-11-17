<?php
define('APPLICATION_ROOT', __DIR__.'/../');

require_once APPLICATION_ROOT.'vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

require APPLICATION_ROOT.'src/Config.php';
require APPLICATION_ROOT.'src/di.php';
require APPLICATION_ROOT.'src/routing.php';

$app->run();
<?php
define('APPLICATION_ROOT', __DIR__ . '/../');
define('APPLICATION_SRC', __DIR__ . '/../src/');

require_once APPLICATION_ROOT . 'vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

require APPLICATION_SRC . 'Config.php';
require APPLICATION_SRC . 'di.php';

return $app;

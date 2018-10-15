<?php
define('APPLICATION_ROOT', __DIR__ . '/../');

$app = require __DIR__.'/../src/bootstrap.php';

require APPLICATION_SRC . 'setup.php';
require APPLICATION_SRC . 'routing.php';

$app->debug = true;
$app->run();

<?php

use Nu3\ProductMigration\Importer;

define('APPLICATION_ROOT', __DIR__.'/../../');

/** @var \Composer\Autoload\ClassLoader() $loader */
$loader = require_once APPLICATION_ROOT.'vendor/autoload.php';
$loader->addPsr4('Nu3\\ProductMigration\\', APPLICATION_ROOT. 'tools/product_migration');

$importer = new Importer();
$importer->init();
$importer->run();
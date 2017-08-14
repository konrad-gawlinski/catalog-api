<?php

use Nu3\ProductMigration\Importer;

define('APPLICATION_ROOT', __DIR__.'/../../');

/** @var \Composer\Autoload\ClassLoader() $loader */
$loader = require_once APPLICATION_ROOT.'vendor/autoload.php';
$loader->addPsr4('Nu3\\ProductMigration\\', APPLICATION_ROOT. 'tools/product_migration');

$importer = new Importer();
$importer->init();
$importer->importProducts(); //slow for DE it takes 2,5min
$importer->importAttributes(); //fast
$importer->importAttributesTypes(); //fast
$importer->importConfigBundleRelation(); //fast
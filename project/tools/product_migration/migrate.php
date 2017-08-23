<?php

use Nu3\ProductMigration\Migrator;

define('APPLICATION_ROOT', __DIR__.'/../../');

/** @var \Composer\Autoload\ClassLoader() $loader */
$loader = require_once APPLICATION_ROOT.'vendor/autoload.php';
$loader->addPsr4('Nu3\\ProductMigration\\', APPLICATION_ROOT. 'tools/product_migration');

$migrator = new Migrator();
$migrator->init();
$migrator->migrateProducts('DE', 'de_DE');
$migrator->initializeProductRelations();
$migrator->createConfigBundleRelations();

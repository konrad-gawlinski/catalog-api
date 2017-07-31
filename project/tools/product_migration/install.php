<?php

define('APPLICATION_ROOT', __DIR__.'/../../');

/** @var \Composer\Autoload\ClassLoader() $loader */
$loader = require_once APPLICATION_ROOT.'vendor/autoload.php';
$loader->addPsr4('Nu3\\ProductMigration\\', APPLICATION_ROOT.'tools/product_migration');


$createTableQuery = <<<QUERY
CREATE TABLE products (
sku VARCHAR PRIMARY KEY,
attributes JSONB
);
QUERY;

$db = new Nu3\ProductMigration\Database();
$con = $db->connect();

pg_query($con, "CREATE SCHEMA migration;");
pg_query($con, "SET search_path TO migration;");
pg_query($con, $createTableQuery);

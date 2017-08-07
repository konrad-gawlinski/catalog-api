<?php

define('APPLICATION_ROOT', __DIR__.'/../../');

/** @var \Composer\Autoload\ClassLoader() $loader */
$loader = require_once APPLICATION_ROOT.'vendor/autoload.php';
$loader->addPsr4('Nu3\\ProductMigration\\', APPLICATION_ROOT.'tools/product_migration');


$createTableQuery = <<<QUERY
CREATE TABLE migration.products (
sku VARCHAR PRIMARY KEY,
product_id INTEGER NOT NULL,
attributes JSONB
);

CREATE TABLE migration.pac_catalog_attribute (
  id INTEGER PRIMARY KEY,
  name VARCHAR NOT NULL UNIQUE
);

CREATE TABLE migration.pac_catalog_attribute_type (
  id INTEGER PRIMARY KEY,
  type VARCHAR NOT NULL,
  fk_attribute INTEGER REFERENCES migration.pac_catalog_attribute(id)
);

CREATE TABLE migration.nu3_catalog_bundle (
  product_idA INTEGER NOT NULL,
  product_idB INTEGER NOT NULL
);
QUERY;

$db = new Nu3\ProductMigration\Database();
$con = $db->connect();

pg_query($con, "CREATE SCHEMA migration;");
pg_query($con, "SET search_path TO migration;");
pg_query($con, $createTableQuery);

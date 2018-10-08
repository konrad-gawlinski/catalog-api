<?php

namespace Nu3\ProductMigration;

use Nu3\ProductMigration\Importer\Database as DatabaseImporter;
use Nu3\ProductMigration\Migrator\Helper;
use Nu3\ProductMigration\Migrator as Command;

class Migrator
{
  private $dbCon;

  function __construct()
  {
    $db = new Database();
    $this->dbCon = $con = $db->connect();
  }

  function init()
  {
    pg_query($this->dbCon, "SET CLIENT_ENCODING TO 'UTF8';");
    pg_query($this->dbCon, "SET search_path TO migration, catalog_sp, catalog;");
  }

  function migrateProducts(string $country, string $language)
  {
    $attributeTypeSelector = new Helper\AttributeTypeSelector();
    $command = new Command\ProductEntity($this->dbCon, $attributeTypeSelector);

    $command->migrateProducts($country, $language);
  }

  function initializeProductRelations()
  {
    $command = new Command\ProductRelations($this->dbCon);
    $command->initializeProductRelations();
  }

  function createConfigBundleRelations()
  {
    $command = new Command\ProductRelations($this->dbCon);
    $command->createConfigBundleRelations();
  }

  function createTrueConfigs()
  {
    $trueConfigExtractor = new Command\TrueConfigExtractor($this->dbCon);
    $trueConfigExtractor->extractTrueConfigs();
  }

  function migrateAttributesOptionsValues(string $country, string $language)
  {
    $attributeOptionTypeSelector = new Helper\AttributeOptionTypeSelector();
    $command = new Command\AttributeOptionsValues($this->dbCon, $attributeOptionTypeSelector);

    $command->migrate($country, $language);
  }
}
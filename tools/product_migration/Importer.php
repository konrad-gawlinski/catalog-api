<?php

namespace Nu3\ProductMigration;

use Nu3\ProductMigration\Importer\Database as DatabaseImporter;
use Nu3\ProductMigration\Importer\JsonReader;

class Importer
{
  /** @var Database */
  private $db;

  private $dbCon;

  function __construct()
  {
    $this->db = new Database();
  }

  function init()
  {
    $this->dbCon = $con = $this->db->connect();
    pg_query($con, "SET CLIENT_ENCODING TO 'UTF8';");
    pg_query($con, "SET search_path TO migration;");
  }

  function importProducts()
  {
    $file = fopen(APPLICATION_ROOT. 'tools/product_migration/data/products_DE.json', 'r');
    $totalSkus = 0;

    $jsonReader = new JsonReader($file, './reader.log');
    $databaseWriter = new DatabaseImporter\ProductWriter($this->dbCon);

    while($product = $jsonReader->readProduct()) {
      $databaseWriter->writeProduct($product);

      ++$totalSkus;
    }

    echo "\nTotal skus: {$totalSkus}\n";

    fclose($file);
  }

  function importAttributes()
  {
    $databaseWriter = new DatabaseImporter\ColumnWriter($this->dbCon);
    $file = fopen(APPLICATION_ROOT. 'tools/product_migration/data/pac_catalog_attribute.csv', 'r');
    $totalItems = 0;

    //ingore first row
    fgetcsv($file, null, ',');

    while (($data = fgetcsv($file, null, ',')) !== false) {
      $databaseWriter->write('pac_catalog_attribute', [
        'id' => ['value' => $data[0], 'type' => 'numeric'],
        'name' => ['value' => $data[1], 'type' => 'text']
      ]);

      ++$totalItems;
    }

    echo "\nTotal attributes: {$totalItems}\n";

    fclose($file);
  }

  function importAttributesTypes()
  {
    $databaseWriter = new DatabaseImporter\ColumnWriter($this->dbCon);
    $file = fopen(APPLICATION_ROOT. 'tools/product_migration/data/pac_catalog_value_type.csv', 'r');
    $totalItems = 0;

    //ingore first row
    fgetcsv($file, null, ',');

    while (($data = fgetcsv($file, null, ',')) !== false) {
      $databaseWriter->write('pac_catalog_attribute_type', [
        'id' => ['value' => $data[0], 'type' => 'numeric'],
        'type' => ['value' => $data[1], 'type' => 'text'],
        'fk_attribute' => ['value' => $data[2], 'type' => 'numeric']
      ]);

      ++$totalItems;
    }

    echo "\nTotal attribute types: {$totalItems}\n";

    fclose($file);
  }

  function importConfigBundleRelation()
  {
    $databaseWriter = new DatabaseImporter\ColumnWriter($this->dbCon);
    $file = fopen(APPLICATION_ROOT. 'tools/product_migration/data/nu3_catalog_bundle.csv', 'r');
    $totalItems = 0;

    //ingore first row
    fgetcsv($file, null, ',');

    while (($data = fgetcsv($file, null, ',')) !== false) {
      $databaseWriter->write('nu3_catalog_bundle', [
        'product_idA' => ['value' => $data[1], 'type' => 'numeric'],
        'product_idB' => ['value' => $data[2], 'type' => 'numeric'],
      ]);

      ++$totalItems;
    }

    echo "\nTotal relations: {$totalItems}\n";

    fclose($file);
  }

  function importAttributeOptionValues()
  {
    $databaseWriter = new DatabaseImporter\ColumnWriter($this->dbCon);
    $file = fopen(APPLICATION_ROOT. 'tools/product_migration/data/pac_catalog_value_option.csv', 'r');
    $totalItems = 0;

    //ingore first row
    fgetcsv($file, null, ',');

    while (($data = fgetcsv($file, null, ',')) !== false) {
      $databaseWriter->write('pac_catalog_attribute_option_value', [
        'fk_attribute_type' => ['value' => $data[2], 'type' => 'numeric'],
        'value' => ['value' => $data[1], 'type' => 'text'],
      ]);

      ++$totalItems;
    }

    echo "\nTotal option values: {$totalItems}\n";

    fclose($file);
  }
}
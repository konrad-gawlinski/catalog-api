<?php

namespace Nu3\ProductMigration;

use Nu3\ProductMigration\Importer\DatabaseWriter;
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

  function run()
  {
    $file = fopen(APPLICATION_ROOT. 'tools/product_migration/products_DE.json', 'r');
    $totalSkus = 0;

    $jsonReader = new JsonReader($file, './reader.log');
    $databaseWriter = new DatabaseWriter($this->dbCon);

    while($product = $jsonReader->readProduct()) {
      $databaseWriter->writeProduct($product);

      ++$totalSkus;
    }

    echo "\nTotal skus: {$totalSkus}\n";

    fclose($file);
  }
}
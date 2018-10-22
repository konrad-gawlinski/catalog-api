<?php

require __DIR__ . '/Database/Source.php';
require __DIR__ . '/Database/Target.php';

class Migrator
{

  /** @var Migration\Database\Source */
  private $sourceDb;

  /** @var Migration\Database\Target */
  private $targetDb;

  function __construct()
  {
    $this->initSourceDB();
    $this->initTargetDB();
  }

  function migrate()
  {
    $skusResult = $this->sourceDb->fetchAllCatalogProductsSkus();

    while ($skuRow = pg_fetch_array($skusResult)) {
      $sku = $skuRow[0];
      $productPropertiesResult = $this->sourceDb->fetchCatalogProduct($sku);

      var_dump($sku);
      while ($properties = pg_fetch_assoc($productPropertiesResult)) {
        list(
          'sku' => $sku,
          'de' => $deProperties,
          'ch' => $chProperties,
          'fr' => $frProperties,
          ) = $properties;

        $extracted = $this->extractGlobal($deProperties, $chProperties, $frProperties);
        $this->targetDb->updateProduct($sku, $extracted['global'], $extracted['de'], $extracted['ch'], $extracted['fr']);
      }
    }
  }

  private function extractGlobal(string $deProperties, string $chProperties, string $frProperties)
  {
    $deProperties = json_decode($deProperties, true);
    $chProperties = json_decode($chProperties, true);
    $frProperties = json_decode($frProperties, true);

    $intersection = array_uintersect_assoc($deProperties, $chProperties, $frProperties, [$this, 'compareAttributeValue']);
    unset($intersection['status']);
    unset($intersection['tax_rate']);

    if (empty($intersection)) {
      $intersection = array_uintersect_assoc($deProperties, $frProperties, [$this, 'compareAttributeValue']);
      unset($intersection['status']);
      unset($intersection['tax_rate']);
    }

    $deDiff = array_udiff_assoc($deProperties, $intersection, [$this, 'compareAttributeValue']);
    $chDiff = array_udiff_assoc($chProperties, $intersection, [$this, 'compareAttributeValue']);
    $frDiff = array_udiff_assoc($frProperties, $intersection, [$this, 'compareAttributeValue']);

    return [
      'de' => $deDiff,
      'ch' => [], //$chDiff,
      'fr' => $frDiff,
      'global' => $intersection
    ];
  }

  private function compareAttributeValue($a, $b)
  {
    $stringA = json_encode($a);
    $stringB = json_encode($b);

    return $stringA === $stringB ? 0 : 1;
  }

  private function initSourceDB()
  {
    $sourceDB = new Migration\Database\Source();
    $sourceDB->connect('catalog-api-database_dev_01-01', '5432', 'catalog_api', 'catalogapi_user', '123456');
    $sourceDB->setSearchPath('catalog');
    $this->sourceDb = $sourceDB;
  }

  private function initTargetDB()
  {
    $targetDB = new Migration\Database\Target();
    $targetDB->connect('catalog-api-database_dev_01-01', '5432', 'catalog_api', 'catalogapi_user', '123456');
    $targetDB->setSearchPath('catalog');
    $this->targetDb = $targetDB;
  }
}

$migrator = new Migrator();
$migrator->migrate();



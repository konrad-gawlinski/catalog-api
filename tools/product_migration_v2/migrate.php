<?php

require __DIR__ . '/Database/Source.php';
require __DIR__ . '/Database/Target.php';
require __DIR__ . '/AttributeTypeSorter.php';

class Migrator
{
  private $attributeTypeSorter;

  /** @var Migration\Database\Source */
  private $sourceDb;

  /** @var Migration\Database\Target */
  private $targetDb;

  private $countryProperties = [];
  private $languageProperties = [];

  function __construct()
  {
    $this->attributeTypeSorter = new Migration\AttributeTypeSorter();
    $this->initSourceDB();
    $this->initTargetDB();
  }

  function setRegion($country, $language)
  {
    $this->targetDb->setCountry($country);
    $this->targetDb->setLanguage($language);
  }

  function migrate()
  {
    $skusResult = $this->sourceDb->fetchAllLegacyProductsSkus();

    while ($skuRow = pg_fetch_array($skusResult)) {
      $sku = $skuRow[0];
      $productPropertiesResult = $this->sourceDb->fetchLegacyProduct($sku);

      while ($property = pg_fetch_assoc($productPropertiesResult)) {
        list(
          'sku' => $sku,
          'variety' => $variety,
          'status' => $status,
          'name' => $attributeName,
          'value' => $attributeValue,
          'type' => $attributeType
          ) = $property;

        $this->countryProperties['status'] = $status;
        $this->addAttribute($attributeType, $attributeName, $attributeValue);
      }

      $this->storeProduct($sku, $variety);
    }
  }

  private function storeProduct($sku, $variety)
  {
    var_dump($sku);
    $this->targetDb->insertProduct($sku, $variety, $this->countryProperties, $this->languageProperties);

    $this->countryProperties = [];
    $this->languageProperties = [];
  }

  private function convertValue($attributeType, $value)
  {
    switch ($attributeType) {
      case 'integer': return intval($value);
      case 'decimal': return floatval($value);
      case 'boolean': return $value === '1' ? true : false;
      case 'option_multi': return explode(' ---- ', $value);
    }

    return $value;
  }

  private function addAttribute($attributeType, $attributeName, $attributeValue)
  {
    $convertedValue = $this->convertValue($attributeType, $attributeValue);
    if ($this->attributeTypeSorter->isLanguage($attributeName)) {
      $this->languageProperties[$attributeName] = $convertedValue;
    } else {
      $this->countryProperties[$attributeName] = $convertedValue;
    }
  }

  private function initSourceDB()
  {
    $sourceDB = new Migration\Database\Source();
    $sourceDB->connect('catalog-api-database_dev_01-01', '5432', 'catalog_api', 'catalogapi_user', '123456');
    $sourceDB->setSearchPath('ch_production_zed');
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
$migrator->setRegion('ch', 'ch_de');
$migrator->migrate();



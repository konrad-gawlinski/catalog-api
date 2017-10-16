<?php

namespace Nu3\ProductMigration\Migrator;

class ProductEntity
{
  use Property\Database;

  /** @var Helper\AttributeTypeSelector */
  private $attributeTypeSelector;

  function __construct($dbCon, Helper\AttributeTypeSelector $selector)
  {
    $this->setDbCon($dbCon);
    $this->attributeTypeSelector = $selector;
  }

  function migrateProducts(string $country, string $language)
  {
    $products = pg_query($this->dbCon, "SELECT * FROM migration.products");

    while ($row = pg_fetch_assoc($products)) {
      $attributes = $this->splitAttributesByType($row);
      $variety = $attributes['global']['variety'];
      $attributes = $this->encodeAttributes($attributes);

      pg_query($this->dbCon, 'INSERT INTO catalog.product_entity' .
        "(sku, type, global, {$country}, {$language}) " .
        "VALUES('{$row['sku']}', '{$variety}', '{$attributes['global']}', '{$attributes['country']}', '{$attributes['language']}')");
    }
  }

  private function splitAttributesByType(array $productRow) : array
  {
    $result = [
      'global' => [],
      'country' => [],
      'language' => []
    ];

    $attributes = json_decode($productRow['attributes'], true);
    foreach ($attributes as $name => $value) {
      $type = 'global';

      if ($this->attributeTypeSelector->isLanguage($name)) {
        $type = 'language';
      } else if ($this->attributeTypeSelector->isCountry($name)) {
        $type = 'country';
      }

      $result[$type][$name] = $value;
    }

    return $result;
  }

  private function encodeAttributes(array $attributes)
  {
    unset($attributes['type']);

    return [
      'global' => str_replace("'", "''", json_encode($attributes['global'])),
      'country' => str_replace("'", "''", json_encode($attributes['country'])),
      'language' => str_replace("'", "''", json_encode($attributes['language']))
    ];
  }
}

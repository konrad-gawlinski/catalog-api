<?php

namespace Nu3\ProductMigration\Migrator;

use Nu3\ProductMigration\Migrator\Helper;

class AttributeOptionsValues
{
  use Property\Database;

  /** @var Helper\AttributeOptionTypeSelector */
  private $attributeOptionTypeSelector;

  function __construct($dbCon, Helper\AttributeOptionTypeSelector $selector)
  {
    $this->setDbCon($dbCon);
    $this->attributeOptionTypeSelector = $selector;
  }

  function migrate(string $country, string $language)
  {
    $attributes = pg_query($this->dbCon, "SELECT a.name as name, jsonb_agg(v.value) as values FROM
      pac_catalog_attribute_option_value v
      JOIN pac_catalog_attribute_type t ON v.fk_attribute_type = t.id
      JOIN pac_catalog_attribute a ON a.id = t.fk_attribute
    GROUP BY a.name
    ORDER BY a.name;");

    while ($row = pg_fetch_assoc($attributes)) {
      pg_query($this->dbCon, "SELECT nu3__create_product_option_value('{$row['name']}')");
      $this->storeValues($row, $country, $language);
    }
  }

  private function storeValues(array $attributeRow, string $country, string $language)
  {
    $column = 'global';
    $attributeName = $attributeRow['name'];
    $attributeValue = str_replace("'", "''", $attributeRow['values']);

    if ($this->attributeOptionTypeSelector->isCountry($attributeName))
      $column = $country;
    else if ($this->attributeOptionTypeSelector->isLanguage($attributeName))
      $column = $language;

    pg_query($this->dbCon, "SELECT nu3__update_product_option_value('{$attributeName}', '{$column}', '{$attributeValue}');");
  }
}
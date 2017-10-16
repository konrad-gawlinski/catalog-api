<?php

namespace Nu3\ProductMigration\Migrator\Helper;

class AttributeOptionTypeSelector
{
  function isGlobal(string $attributeName) : bool
  {
    static $list = [
      'base_category' => true,
      'country_of_origin_iso2' => true,
      'label_language' => true,
      'main_label_language' => true,
      'manufacturer' => true,
      'manufacturer_multi' => true,
      'medical_device_class' => true,
      'package' => true,
      'packaging_material' => true,
      'product_type' => true,
      'type' => true,
      'unit_price_relation' => true
    ];

    return isset($list[$attributeName]);
  }

  function isCountry(string $attributeName) : bool
  {
    static $list = [
      'seo_meta_robots_prod' => true,
      'tax_rate' => true
    ];

    return isset($list[$attributeName]);
  }

  function isLanguage(string $attributeName) : bool
  {
    static $list = [
      'age_group_max_unit' => true,
      'age_group_min_unit' => true,
      'best_before_unit' => true,
      'daily_dosage_unit' => true,
      'dietary_treatment' => true,
      'dosage_form' => true,
      'expiration_placement' => true,
      'expiration_type' => true,
      'net_unit' => true,
      'portions_unit' => true,
      'short_dosage_form' => true,
      'special_treatment' => true,
      'stock_status' => true,
      'sweetener' => true,
      'unit' => true,
      'weight_unit' => true
    ];

    return isset($list[$attributeName]);
  }

}
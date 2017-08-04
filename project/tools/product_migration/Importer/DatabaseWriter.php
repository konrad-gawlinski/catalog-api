<?php

namespace Nu3\ProductMigration\Importer;

class DatabaseWriter
{
  private $con;

  function __construct($dbConnection)
  {
    $this->con = $dbConnection;
  }

  public function writeProduct(array $product)
  {
    $sku = $product['sku'];
    $json = json_encode($this->prepareAttributes($product));
    $json = str_replace("'", "''", $json);

    pg_query($this->con,
      "INSERT INTO products (sku, attributes) VALUES('{$sku}', '{$json}');"
    );
  }

  private function prepareAttributes(array $product) : array
  {
    $attributes = [
      'product_id' => $product['product_id'],
      'status' => $product['status'],
      'variety' => $product['variety'],
    ];

    foreach ($product['attributes'] as $value) {
      $_value = $value['value'];
      $attributeName = $value['name'];

      if ($this->isBooleanAttribute($attributeName)) $_value = boolval($_value);
      else if ($this->isIntegerAttribute($attributeName)) $_value = intval($_value);
      else {
        $_value = $this->unescapeJsonNotAllowedCharacters($_value);
      }

      $attributes[$attributeName] = $_value;
    }

    return $attributes;
  }

  private function unescapeJsonNotAllowedCharacters(string $input) : string
  {
    $search = ["\\n", "\\r", "\\t", "\\f", "\\b"];
    $replace = ["\n", "\r", "\t", "\x08", "\x0c"];
    $output = str_replace($search, $replace, $input);

    return $output;
  }

  private function isIntegerAttribute($attributeName) : bool
  {
    static $list = [
      'weight' => true,
      'portions' => true,
      'drained_weight' => true,
      'ean' => true,
      'customs_tariff_id_ch' => true,
      'customs_tariff_id_de' => true,
      'customs_tariff_id_no' => true,
      'ean_self' => true,
      'gross_weight' => true,
      'net_quantity' => true,
      'minimum_best_before_date_days' => true,
      'best_before' => true,
      'ce_number' => true,
      'daily_dosage' => true,
      'caffeine' => true,
      'stock_delivery_days' => true,
      'stock_reach' => true,
      'storage_temperature' => true,
      'base_gross_price' => true,
      'final_gross_price' => true,
      'recommended_retail_price' => true,
      'max_price_reduction_of_rrp' => true,
      'best_before_price' => true,
    ];

    return isset($list[$attributeName]);
  }

  private function isBooleanAttribute($attributeName) : bool
  {
    static $list = [
      'own_brand_product' => true,
      'bundle_only' => true,
      'cooled_product' => true,
      'customs_preferred_ch' => true,
      'customs_preferred_no' => true,
      'use_portions' => true,
      'multi_pack' => true,
      'icon_bio' => true,
      'icon_certified_natural' => true,
      'icon_cologne_list' => true,
      'icon_dye' => true,
      'icon_fairtrade' => true,
      'icon_flavour' => true,
      'icon_fructose_free' => true,
      'icon_gelatine' => true,
      'icon_gluten' => true,
      'icon_hypo' => true,
      'icon_lactose' => true,
      'icon_low_in_sugar' => true,
      'icon_mineral_oil' => true,
      'icon_natural' => true,
      'icon_no_added_sugar' => true,
      'icon_no_alcohol' => true,
      'icon_no_aluminum' => true,
      'icon_no_animal_experiments' => true,
      'icon_no_flavour_enhancers' => true,
      'icon_no_genetic_engineering' => true,
      'icon_no_nanotechnology' => true,
      'icon_no_parabens' => true,
      'icon_perfum' => true,
      'icon_preservative' => true,
      'icon_raw' => true,
      'icon_silicon' => true,
      'icon_sugar' => true,
      'icon_sugar_free' => true,
      'icon_sulfat' => true,
      'icon_sweetner' => true,
      'icon_vegan' => true,
      'icon_vegetarien' => true,
      'icon_yeast' => true,
      'icon_demeter' => true,
      'icon_low_carb' => true,
      'canonical_version' => true,
      'is_beverage' => true,
      'is_gluten_free' => true,
      'is_single_source' => true,
      'is_sweetener' => true,
      'only_rdd' => true,
      'warning_aspartam' => true,
      'warning_excessive_laxative' => true,
      'warning_laxative' => true,
      'warning_licorice' => true,
      'warning_phenylalanin' => true,
      'warning_pressure_licorice' => true,
      'warning_sugar' => true,
      'warning_sweetener' => true,
      'out_of_stock' => true,
      'status_particularity' => true,
    ];

    return isset($list[$attributeName]);
  }
}
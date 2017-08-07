<?php

namespace Nu3\ProductMigration\Importer\Database;

class ProductWriter
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
      'cost' => true,
      'dosage' => true,
      'qty_notify' => true,
      'rda' => true,
      'boost_health' => true,
      'boost_beauty' => true,
      'boost_nature' => true,
      'boost_sport' => true,
      'prime_cost' => true,
      'max_quantity' => true,
      'rating_one_star' => true,
      'rating_two_stars' => true,
      'rating_three_stars' => true,
      'rating_four_stars' => true,
      'rating_five_stars' => true,
      'text_review_amount' => true,
      'stock_warning_amount' => true,
      'feed_export_category' => true,
      'package_gross_weight' => true,
      'package_net_weight' => true,
      'package_height' => true,
      'package_width' => true,
      'package_length' => true,
      'base_gross_price' => true,
      'base_nett_price' => true,
      'final_gross_price' => true,
      'final_nett_price' => true,
      'recommended_retail_price' => true,
      'displayable_stock' => true,
      'physical_stock' => true,
      'reserved_stock' => true,
      'saleable_stock' => true,
      'minimum_best_before_date_days' => true,
      'stock_delivery_threshold' => true,
      'stock_delivery_days' => true,
      'age_group_min' => true,
      'age_group_max' => true,
      'best_before' => true,
      'caffeine' => true,
      'drained_weight' => true,
      'gross_weight' => true,
      'net_quantity' => true,
      'daily_dosage' => true,
      'portions' => true,
      'manufacturer_data' => true,
    ];

    return isset($list[$attributeName]);
  }

  private function isBooleanAttribute($attributeName) : bool
  {
    static $list = [
      'pharmacy' => true,
      'promotion' => true,
      'leaflet' => true,
      'top_product' => true,
      'top25' => true,
      'icon_bio' => true,
      'icon_dye' => true,
      'icon_perfum' => true,
      'icon_silicon' => true,
      'icon_sweetner' => true,
      'icon_sugar' => true,
      'icon_flavour' => true,
      'icon_preservative' => true,
      'icon_gelatine' => true,
      'icon_gluten' => true,
      'icon_yeast' => true,
      'icon_hypo' => true,
      'icon_lactose' => true,
      'icon_natural' => true,
      'icon_vegan' => true,
      'icon_vegetarien' => true,
      'icon_cologne_list' => true,
      'icon_mineral_oil' => true,
      'icon_sulfat' => true,
      'icon_sugar_free' => true,
      'icon_certified_natural' => true,
      'to_be_cooled' => true,
      'cooled_product' => true,
      'glass_product' => true,
      'single_sku' => true,
      'best_before_date' => true,
      'bundle_only' => true,
      'customs_preferred_ch' => true,
      'customs_preferred_no' => true,
      'is_beverage' => true,
      'is_gluten_free' => true,
      'is_single_source' => true,
      'is_sweetener' => true,
      'warning_sweetener' => true,
      'warning_sugar' => true,
      'warning_excessive_laxative' => true,
      'warning_laxative' => true,
      'warning_licorice' => true,
      'warning_pressure_licorice' => true,
      'warning_aspartam' => true,
      'warning_phenylalanin' => true,
      'only_rdd' => true,
      'icon_low_in_sugar' => true,
      'icon_no_added_sugar' => true,
      'icon_fructose_free' => true,
      'icon_no_flavour_enhancers' => true,
      'icon_no_alcohol' => true,
      'icon_raw' => true,
      'icon_fairtrade' => true,
      'icon_no_nanotechnology' => true,
      'icon_no_genetic_engineering' => true,
      'icon_no_parabens' => true,
      'icon_no_aluminum' => true,
      'icon_no_animal_experiments' => true,
      'nutrition_tool_data_available' => true,
      'icon_demeter' => true,
      'icon_low_carb' => true,
      'icon_no_aspartam' => true,
      'icon_derma' => true,
      'icon_no_allergenes' => true,
    ];

    return isset($list[$attributeName]);
  }
}
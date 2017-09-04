<?php

namespace Nu3\ProductMigration;

use Nu3\ProductMigration\Importer\Database as DatabaseImporter;
use Nu3\ProductMigration\Migrator\TrueConfigExtractor;

class Migrator
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
    pg_query($con, "SET search_path TO migration, catalog_sp, catalog;");
  }

  function migrateProducts(string $country, string $language)
  {
    $products = pg_query($this->dbCon, "SELECT * FROM migration.products");

    while ($row = pg_fetch_assoc($products)) {
      $attributes = $this->splitAttributesByType($row);
      $variety = $attributes['global']['variety'];
      $attributes = $this->encodeAttributes($attributes);

      pg_query('INSERT INTO catalog.product_entity' .
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

      if ($this->isLanguageAttribute($name)) {
        $type = 'language';
      } else if ($this->isCountryAttribute($name)) {
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

  private function isGlobalAttribute(string $attributeName) : array
  {
    static $list = [
      'manufacturer' => true,
      'weight' => true,
      'weight_unit' => true,
      'own_brand_product' => true,
      'base_category' => true,
      'product_group_l1' => true,
      'product_group_l2' => true,
      'product_group_l3' => true,
      'prdnr' => true,
      'amount' => true,
      'variety' => true,
      'portions' => true,
      'portions_unit' => true,
      'unit' => true,
      'drained_weight' => true,
      'package' => true,
      'glass_product' => true,
      'cooled_product' => true,
      'package_gross_weight' => true,
      'package_height' => true,
      'package_length' => true,
      'package_width' => true,
      'ean' => true,
      'pzn' => true,
      'label_language' => true,
      'manufacturer_data' => true,
      'country_of_origin_iso2' => true,
      'customs_preferred_ch' => true,
      'customs_preferred_no' => true,
      'customs_tariff_id_ch' => true,
      'customs_tariff_id_de' => true,
      'customs_tariff_id_no' => true,
      'multi_pack' => true,
      'ean_self' => true,
      'main_label_language' => true,
      'bioreg' => true,
      'package_net_weight' => true,
      'gross_weight' => true,
      'net_quantity' => true,
      'net_unit' => true,
      'packaging_material' => true,
      'config_skus_of_bundle' => true,
      'bundle_type' => true,
      'category' => true,
      'dosage_form' => true,
      'short_dosage_form' => true,
      'target_group' => true,
      'best_before_date' => true,
      'flavour' => true,
      'scent' => true,
      'multipack_portions' => true,
      'application' => true,
      'mpn' => true,
      'Comment' => true,
      'manufacturer_multi' => true,
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
      'expiration_placement' => true,
      'expiration_type' => true,
      'minimum_best_before_date_days' => true,
      'best_before' => true,
      'best_before_unit' => true,
      'product_type' => true,
      'ce_number' => true,
      'daily_dosage' => true,
      'daily_dosage_added_ingredient' => true,
      'daily_dosage_unit' => true,
      'is_beverage' => true,
      'is_gluten_free' => true,
      'is_single_source' => true,
      'is_sweetener' => true,
      'dietary_treatment' => true,
      'caffeine' => true,
      'age_group_max' => true,
      'age_group_max_unit' => true,
      'age_group_min' => true,
      'age_group_min_unit' => true,
      'medical_device_class' => true,
      'only_rdd' => true,
      'special_treatment' => true,
      'warning_aspartam' => true,
      'warning_excessive_laxative' => true,
      'warning_laxative' => true,
      'warning_licorice' => true,
      'warning_phenylalanin' => true,
      'warning_pressure_licorice' => true,
      'warning_sugar' => true,
      'warning_sweetener' => true,
      'sweetener' => true,
      'nutrition_tool_data_available' => true,
      'stock_amount_in_stock' => true,
      'stock_reserved_inventory' => true,
      'stock_saleable_inventory' => true,
      'stock_delivery_days' => true,
      'stock_delivery_threshold' => true,
      'stock_status' => true,
      'stock_warning_amount' => true,
      'availability' => true,
      'out_of_stock' => true,
      'available_again_at_supplier' => true,
      'reason_for_unavailability' => true,
      'back_in_stock_date' => true,
      'current_supplier' => true,
      'date_of_last_notification' => true,
      'global_cost' => true,
      'stock_reach' => true,
      'first_order_date' => true,
      'inbound_date' => true,
      'storage_temperature' => true,
      'best_before_date_sale' => true,
      'best_before_sale_text' => true,
      'global_name' => true,
      'brand' => true,
      'sub_brand' => true,
      'product_line' => true,
      'PF_main_ingredient' => true,
      'product_family' => true,
      'unit_price_relation' => true,
    ];

    return isset($list[$attributeName]);
  }

  private function isCountryAttribute($attributeName)
  {
    static $list = [
      'bundle_only' => true,
      'use_portions' => true,
      'feed_export_category' => true,
      'canonical_version' => true,
      'seo_meta_robots_prod' => true,
      'canonical_url' => true,
      'cost' => true,
      'related_products' => true,
      'alternative_product' => true,
      'cross_selling_products' => true,
      'base_gross_price' => true,
      'tax_rate' => true,
      'final_gross_price' => true,
      'recommended_retail_price' => true,
      'max_price_reduction_of_rrp' => true,
      'pricing_particularity' => true,
      'best_before_price' => true,
      'CM1_margin_product' => true,
      'best_before_sale' => true,
      'reason_for_change' => true,
      'performance_cluster' => true,
      'status' => true,
      'status_particularity' => true,
      'particularity_reason' => true,
      'brand_potential' => true,
    ];

    return isset($list[$attributeName]);
  }

  private function isLanguageAttribute($attributeName)
  {
    static $list = [
      'name' => true,
      'local_name_add' => true,
      'partners_description' => true,
      'description' => true,
      'short_description' => true,
      'product_specific_description' => true,
      'extended_content_section_1' => true,
      'extended_content_section_2' => true,
      'extended_content_section_3' => true,
      'extended_content_section_4' => true,
      'extended_content_teaser_image' => true,
      'sem_keywords' => true,
      'meta_title' => true,
      'meta_description' => true,
      'url_key' => true,
      'nutrition_description' => true,
      'medical_properties' => true,
      'purpose' => true,
      'user_guide' => true,
      'allergen_warnings' => true,
      'side_effects' => true,
      'contraindications' => true,
      'special_nutrition_properties' => true,
      'special_precaution' => true,
      'storage_advice' => true,
      'waste_handling' => true,
      'key_product_benefit_1' => true,
      'key_product_benefit_2' => true,
      'key_product_benefit_3' => true,
      'PF_product_type' => true,
      'PF_name' => true,
    ];

    return isset($list[$attributeName]);
  }

  function initializeProductRelations()
  {
    $products = pg_query($this->dbCon, "SELECT id FROM catalog.product_entity");

    while ($row = pg_fetch_assoc($products)) {
      pg_query("SELECT nu3__ct_create_node('{$row['id']}');");
    }
  }

  function createConfigBundleRelations()
  {
    $products = pg_query($this->dbCon,
      "SELECT b.product_ida as bundle_id, b.product_idb as config_id,
              p1.sku as bundle_sku, p2.sku as config_sku,
              e1.id as bundle_entity, e2.id as config_entity 
        FROM nu3_catalog_bundle b JOIN products p1 ON b.product_ida=p1.product_id
        JOIN products p2 ON b.product_idb=p2.product_id
        JOIN catalog.product_entity e1 ON p1.sku=e1.sku
        JOIN catalog.product_entity e2 ON p2.sku=e2.sku;"
    );

    while ($row = pg_fetch_assoc($products)) {
      pg_query("SELECT nu3__ct_make_node_a_child({$row['bundle_entity']}, {$row['config_entity']}, 1);");
    }
  }

  function createTrueConfigs()
  {
    $trueConfigExtractor = new TrueConfigExtractor($this->dbCon);
    $trueConfigExtractor->run();
  }
}
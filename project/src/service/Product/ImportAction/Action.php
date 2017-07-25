<?php

namespace Nu3\Service\Product\ImportAction;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action
{
  function create_flat_structure(): HttpResponse
  {
    global $app;
    $db = $app['database.connection'];
    pg_query($db->db(), "SET CLIENT_ENCODING TO 'UTF8';");

    $file = fopen(APPLICATION_ROOT. 'products_DE.json', 'r');

    while ($product = fgets($file)) {
      $result = json_decode($product, true);
      $sku = $result['sku'];
      $productId = $result['product_id'];

      $output = [];
      $output['status'] = $result['status'];
      $output['variety'] = $result['variety'];

      foreach ($result['attributes'] as $attribute => $value) {
        $output[$value['name']] = addslashes($value['value']);
      }

      $json = json_encode($output);
      pg_query($db->db(), "INSERT INTO public.pure_data (sku, product_id, attributes) VALUES('{$sku}', {$productId}, '{$json}');");
    }

    fclose($file);
    return new HttpResponse('', 200);
  }

  function import(): HttpResponse
  {
    global $app;
    $db = $app['database.connection'];
    pg_query($db->db(), "SET CLIENT_ENCODING TO 'UTF8';");

    $productFamilyResult = pg_query($db->db(), "SELECT attributes->>'product_family' as product_family, string_agg(sku, ',') as skus FROM pure_data GROUP BY attributes->>'product_family';");

    while ($line = pg_fetch_array($productFamilyResult, null, PGSQL_ASSOC)) {
      $pf = $line['product_family'];
      $skus = $line['skus'];
      $formatedSkus = "'". str_replace(',', "','", $skus) . "'";

      $productsResult = pg_query($db->db(), "SELECT sku, attributes FROM pure_data WHERE sku IN ({$formatedSkus})");
      $products = pg_fetch_all($productsResult);

      $attributes = [];
      $productFamilyAttributes = [];
      foreach ($products as $product) {
        $sku = $product['sku'];
        $attributes[$sku] = $attr = json_decode($product['attributes'], true);

        $intersection = array_intersect_key($attr, $this->getProductFamilyAttributes());
        foreach ($intersection as $name => $value) {
          if (empty($productFamilyAttributes[$name])) {
            $productFamilyAttributes[$name] = $value;
          }
        }
      }

      $insertResult = pg_query($db->db(), 'INSERT INTO product_family (attributes) VALUES (\''. json_encode($productFamilyAttributes) .'\');');
      if ($insertResult) {
        $lastVal = pg_fetch_all(pg_query($db->db(), 'SELECT lastval();'));
        $lastVal = reset($lastVal)['lastval'];
        foreach ($attributes as $key => $value) {
          $globalAttributes = json_encode(array_intersect_key($value, $this->getGlobalAttributes()));
          $localAttributes = json_encode(array_intersect_key($value, $this->getLocalAttributes()));

          pg_query($db->db(), "INSERT INTO products (sku, parent, attributes) VALUES ('{$key}', {$lastVal}, '{$globalAttributes}');");
          pg_query($db->db(), "INSERT INTO per_country_attributes (sku, de) VALUES ('{$key}', '{$globalAttributes}');");
          pg_query($db->db(), "INSERT INTO per_locale_attributes (sku, de_de) VALUES ('{$key}', '{$localAttributes}');");
        }
      }
    }

    return new HttpResponse('', 200);
  }

  private function getProductFamilyAttributes()
  {
    return [
      'manufacturer' => '',
      'own_brand_product' => '',
      'product_group_l1' => '',
      'product_group_l2' => '',
      'product_group_l3' => '',
      'manufacturer_data' => '',
      'category' => '',
      'short_dosage_form' => '',
      'description' => '',
      'short_description' => '',
      'product_type' => '',
      'is_beverage' => '',
      'is_single_source' => '',
      'is_sweetener' => '',
      'age_group_max' => '',
      'age_group_max_unit' => '',
      'age_group_min' => '',
      'age_group_min_unit' => '',
      'purpose' => '',
      'user_guide' => '',
      'storage_advice' => '',
      'waste_handling' => '',
      'storage_temperature' => '',
      'global_name' => '',
      'brand' => '',
      'sub_brand' => '',
      'key_product_benefit_1' => '',
      'key_product_benefit_2' => '',
      'key_product_benefit_3' => '',
      'brand_potential' => ''
    ];
  }

  private function getGlobalAttributes()
  {
    return [
      'manufacturer' => '',
      'weight' => '',
      'weight_unit' => '',
      'own_brand_product' => '',
      'base_category' => '',
      'product_group_l1' => '',
      'product_group_l2' => '',
      'product_group_l3' => '',
      'amount' => '',
      'variety' => '',
      'portions' => '',
      'portions_unit' => '',
      'unit' => '',
      'drained_weight' => '',
      'package' => '',
      'glass_product' => '',
      'cooled_product' => '',
      'package_gross_weight' => '',
      'package_height' => '',
      'package_length' => '',
      'package_width' => '',
      'ean' => '',
      'pzn' => '',
      'label_language' => '',
      'manufacturer_data' => '',
      'country_of_origin_iso2' => '',
      'customs_preferred_ch' => '',
      'customs_preferred_no' => '',
      'customs_tariff_id_ch' => '',
      'customs_tariff_id_de' => '',
      'customs_tariff_id_no' => '',
      'multi_pack' => '',
      'ean_self' => '',
      'main_label_language' => '',
      'bioreg' => '',
      'package_net_weight' => '',
      'gross_weight' => '',
      'net_quantity' => '',
      'net_unit' => '',
      'packaging_material' => '',
      'config_skus_of_bundle' => '',
      'bundle_type' => '',
      'category' => '',
      'dosage_form' => '',
      'short_dosage_form' => '',
      'target_group' => '',
      'best_before_date' => '',
      'flavour' => '',
      'scent' => '',
      'multipack_portions' => '',
      'application' => '',
      'mpn' => '',
      'Comment' => '',
      'manufacturer_multi' => '',
      'icon_bio' => '',
      'icon_certified_natural' => '',
      'icon_cologne_list' => '',
      'icon_dye' => '',
      'icon_fairtrade' => '',
      'icon_flavour' => '',
      'icon_fructose_free' => '',
      'icon_gelatine' => '',
      'icon_gluten' => '',
      'icon_hypo' => '',
      'icon_lactose' => '',
      'icon_low_in_sugar' => '',
      'icon_mineral_oil' => '',
      'icon_natural' => '',
      'icon_no_added_sugar' => '',
      'icon_no_alcohol' => '',
      'icon_no_aluminum' => '',
      'icon_no_animal_experiments' => '',
      'icon_no_flavour_enhancers' => '',
      'icon_no_genetic_engineering' => '',
      'icon_no_nanotechnology' => '',
      'icon_no_parabens' => '',
      'icon_perfum' => '',
      'icon_preservative' => '',
      'icon_raw' => '',
      'icon_silicon' => '',
      'icon_sugar' => '',
      'icon_sugar_free' => '',
      'icon_sulfat' => '',
      'icon_sweetner' => '',
      'icon_vegan' => '',
      'icon_vegetarien' => '',
      'icon_yeast' => '',
      'icon_demeter' => '',
      'icon_low_carb' => '',
      'expiration_placement' => '',
      'expiration_type' => '',
      'minimum_best_before_date_days' => '',
      'best_before' => '',
      'best_before_unit' => '',
      'product_type' => '',
      'ce_number' => '',
      'daily_dosage' => '',
      'daily_dosage_added_ingredient' => '',
      'daily_dosage_unit' => '',
      'is_beverage' => '',
      'is_gluten_free' => '',
      'is_single_source' => '',
      'is_sweetener' => '',
      'dietary_treatment' => '',
      'caffeine' => '',
      'age_group_max' => '',
      'age_group_max_unit' => '',
      'age_group_min' => '',
      'age_group_min_unit' => '',
      'medical_device_class' => '',
      'only_rdd' => '',
      'special_treatment' => '',
      'warning_aspartam' => '',
      'warning_excessive_laxative' => '',
      'warning_laxative' => '',
      'warning_licorice' => '',
      'warning_phenylalanin' => '',
      'warning_pressure_licorice' => '',
      'warning_sugar' => '',
      'warning_sweetener' => '',
      'sweetener' => '',
      'nutrition_tool_data_available' => '',
      'stock_amount_in_stock' => '',
      'stock_reserved_inventory' => '',
      'stock_saleable_inventory' => '',
      'stock_delivery_days' => '',
      'stock_delivery_threshold' => '',
      'stock_status' => '',
      'stock_warning_amount' => '',
      'availability' => '',
      'out_of_stock' => '',
      'available_again_at_supplier' => '',
      'reason_for_unavailability' => '',
      'back_in_stock_date' => '',
      'current_supplier' => '',
      'date_of_last_notification' => '',
      'global_cost' => '',
      'stock_reach' => '',
      'first_order_date' => '',
      'inbound_date' => '',
      'storage_temperature' => '',
      'best_before_date_sale' => '',
      'best_before_sale_text' => '',
      'global_name' => '',
      'brand' => '',
      'sub_brand' => '',
      'product_line' => '',
      'PF_main_ingredient' => '',
      'type' => '',
      'sku' => '',
      'product_family' => '',
      'unit_price_relation' => '',
    ];
  }

  private function getLocalAttributes()
  {
    return [
      'name' => '',
      'bundle_only' => '',
      'use_portions' => '',
      'feed_export_category' => '',
      'partners_description' => '',
      'description' => '',
      'short_description' => '',
      'product_specific_description' => '',
      'image_label' => '',
      'sem_keywords' => '',
      'canonical_version' => '',
      'meta_title' => '',
      'meta_description' => '',
      'seo_meta_robots_prod' => '',
      'url_key' => '',
      'canonical_url' => '',
      'nutrition_description' => '',
      'medical_properties' => '',
      'purpose' => '',
      'user_guide' => '',
      'allergen_warnings' => '',
      'side_effects' => '',
      'contraindications' => '',
      'special_nutrition_properties' => '',
      'special_precaution' => '',
      'storage_advice' => '',
      'waste_handling' => '',
      'cost' => '',
      'related_products' => '',
      'alternative_product' => '',
      'cross_selling_products' => '',
      'base_gross_price' => '',
      'tax_rate' => '',
      'final_gross_price' => '',
      'recommended_retail_price' => '',
      'max_price_reduction_of_rrp' => '',
      'pricing_particularity' => '',
      'best_before_price' => '',
      'CM1_margin_product' => '',
      'best_before_sale' => '',
      'reason_for_change' => '',
      'performance_cluster' => '',
      'key_product_benefit_1' => '',
      'key_product_benefit_2' => '',
      'key_product_benefit_3' => '',
      'status' => '',
      'status_particularity' => '',
      'particularity_reason' => '',
      'brand_potential' => '',
      'shop' => '',
    ];
  }
}

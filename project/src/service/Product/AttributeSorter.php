<?php

namespace Nu3\Service\Product;

class AttributeSorter
{
  const KEY_GLOBAL = 'global';

  /** @var array */
  private $countryAttributes;

  /** @var array */
  private $languageAttributes;

  function sort($country, $language, $attributes) : array
  {
    $sortedList = [];

    foreach ($attributes as $name => $value) {
      $key = self::KEY_GLOBAL;

      if ($this->isLanguageAttribute($name))
        $key = $language;
      else if ($this->isCountryAttribute($name))
        $key = $country;

      $sortedList[$key][$name] = $value;
    }

    return $sortedList;
  }

  private function isCountryAttribute($attributeName) : bool
  {
    $list = $this->loadCountryAttributesList();

    return isset($list[$attributeName]);
  }

  private function isLanguageAttribute($attributeName) : bool
  {
    $list = $this->loadLanguageAttributesList();

    return isset($list[$attributeName]);
  }

  private function loadCountryAttributesList() : array
  {
    if (!$this->countryAttributes) {
      $this->countryAttributes = $this->getCountryAttributes();
    }

    return $this->countryAttributes;
  }


  private function loadLanguageAttributesList() : array
  {
    if (!$this->languageAttributes) {
      $this->languageAttributes = $this->getLanguageAttributes();
    }

    return $this->languageAttributes;
  }

  private function getCountryAttributes() : array
  {
    return [
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
  }

  private function getLanguageAttributes() : array
  {
    return [
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
  }
}

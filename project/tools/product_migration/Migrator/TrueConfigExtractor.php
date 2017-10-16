<?php
namespace Nu3\ProductMigration\Migrator;

class TrueConfigExtractor
{
  use Property\Database;

  private static $stdClass;

  function __construct($dbCon)
  {
    $this->setDbCon($dbCon);
    self::$stdClass = new \stdClass();
  }

  function extractTrueConfigs()
  {
    $productFamilies = pg_query($this->dbCon,
      "SELECT array_agg(id) as product_families FROM product_entity
        WHERE global->>'product_family' IS NOT NULL
        GROUP BY global->>'product_family';"
    );

    while ($productFamilyRow = pg_fetch_assoc($productFamilies)) {
      $productIds = substr($productFamilyRow['product_families'], 1, -1);
      //no need to create true_config if there is only one product in the same product family
      if (isset(explode(',', $productIds)[2])) {
        $products = $this->fetchProductFamilyProducts($productIds);

        list($trueConfig, $reducedProducts) = $this->extractTrueConfig($products);
        $this->save($trueConfig, $reducedProducts);
      }
    }
  }

  private function fetchProductFamilyProducts(string $productIds) : array
  {
    $productFamilyProducts = [];
    $products = pg_query($this->dbCon, "SELECT * FROM product_entity WHERE id IN ({$productIds})");
    while ($product = pg_fetch_assoc($products)) {
      $decodedProduct = $this->jsonDecode($product);
      $productFamilyProducts[] = $decodedProduct;
    }

    return $productFamilyProducts;
  }

  private function jsonDecode(array $product)
  {
    $decodedProduct = $product;
    foreach (['global', 'de', 'de_de'] as $key) {
      $decodedProduct[$key] = json_decode($decodedProduct[$key], true);
    }

    return $decodedProduct;
  }

  private function escapeProductProperties(array $product)
  {
    $properties = [];
    foreach (['global', 'de', 'de_de'] as $key) {
      $properties[$key] = $product[$key] ?: self::$stdClass;
    }

    return str_replace("'", "''", json_encode($properties));
  }

  private function extractTrueConfig($products)
  {
    $trueConfig = [
      'id' => null,
      'sku' => null,
      'type' => 'True_Config',
    ];

    $valueCompareCallback = [$this, 'multiDimensionalArrayCompare'];

    foreach (['global', 'de', 'de_de'] as $key) {
      $commonAttributes = $this->identifyCommonAttributes($products, $key, $valueCompareCallback);
      $trueConfig[$key] = $commonAttributes;

      foreach ($products as &$product) {
        $product[$key] = array_udiff_assoc($product[$key], $commonAttributes, $valueCompareCallback);
      }
    }

    $trueConfig['global']['product_family'] = $products[0]['global']['product_family'];

    return [$trueConfig, $products];
  }

  private function identifyCommonAttributes(array $products, string $key, callable $uintersect) : array
  {
    $attributes = array_column($products, $key);
    $attributes[] = $uintersect;
    $commonAttributes = call_user_func_array('array_uintersect_assoc', $attributes);

    return $this->skipOwnedAttributes($commonAttributes);
  }

  private function multiDimensionalArrayCompare($a, $b) : bool
  {
    $_a = $a;
    $_b = $b;

    if (is_array($a)) $_a = implode('|', $a);
    if (is_array($b)) $_b = implode('|', $b);

    return (string)$_a === (string)$_b;
  }

  private function skipOwnedAttributes(array $input) : array
  {
    unset($input['status']);
    unset($input['product_family']);
    unset($input['variety']);
    unset($input['bundle_only']);

    return $input;
  }

  private function save(array $trueConfig, array $productFamilyProducts)
  {
    pg_query($this->dbCon, 'START TRANSACTION');
      $trueConfigId = $this->saveTrueConfig($trueConfig);
      $this->createProductRelationNode($trueConfigId);

      $productIds = $this->overwriteProducts($productFamilyProducts);
      foreach ($productIds as $productId) {
        pg_query($this->dbCon, "SELECT nu3__ct_make_node_a_child({$trueConfigId}, {$productId}, 1);");
      }

    pg_query($this->dbCon, 'COMMIT');
  }

  /**
   * @param array $trueConfig
   *
   * @return int created true config's id
   */
  private function saveTrueConfig(array $trueConfig) : int
  {
    $properties = $this->escapeProductProperties($trueConfig);

    $result = pg_query($this->dbCon,
      "SELECT nu3__create_product(null, '{$trueConfig['type']}', '{$properties}');"
    );

    return pg_fetch_row($result)[0];
  }

  private function createProductRelationNode(int $id)
  {
    pg_query($this->dbCon, "SELECT nu3__ct_create_node('{$id}');");
  }

  /**
   * @param array $products
   *
   * @return array updated product ids
   */
  private function overwriteProducts(array $products) : array
  {
    $productIds = [];

    foreach ($products as $product) {
      $properties = $this->escapeProductProperties($product);
      $productIds[] = $product['id'];

      pg_query($this->dbCon, "SELECT nu3__overwrite_product({$product['id']}, '{$properties}')");
    }

    return $productIds;
  }
}
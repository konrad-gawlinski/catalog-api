<?php
namespace Migration\Database;

class Source
{
  private $connection = null;

  function connect(string $host, string $port, string $dbname, string $user, string $password)
  {
    $this->connection = pg_connect("host={$host} port={$port} dbname={$dbname} user={$user} password={$password} connect_timeout=1", PGSQL_CONNECT_FORCE_NEW);

    if (!$this->connection)
      throw new \Exception('Could not connect: ' . pg_last_error());

    return $this->connection;
  }

  function setSearchPath(string $searchPath)
  {
    $result = pg_query($this->connection, "SELECT set_config('search_path', '{$searchPath}', false);");

    if ($result === false)
      throw new \Exception("Could not set search_path config '{$searchPath}': " . pg_last_error());
  }

  function con()
  {
    return $this->connection;
  }

  function fetchAllLegacyProductsSkus()
  {
    $query = 'SELECT sku FROM pac_catalog_product';

    $result = pg_query($this->connection, $query);
    if ($result === false)
      throw new \Exception("Could not fetch product skus: " . pg_last_error());

    return $result;
  }

  function fetchAllCatalogProductsSkus()
  {
    $query = 'SELECT sku FROM products;';

    $result = pg_query($this->connection, $query);
    if ($result === false)
      throw new \Exception("Could not fetch product skus: " . pg_last_error());

    return $result;
  }


  function fetchLegacyProduct($sku)
  {
    $query = <<<SQL
SELECT * FROM pac_catalog_product prod JOIN LATERAL (
  SELECT prod.sku, prod.id_catalog_product AS product_id, attr.name AS name, attr.id_catalog_attribute AS id, val.value::text, 'text' AS type FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_text val ON attr.id_catalog_attribute = val.fk_catalog_attribute
  WHERE val.fk_catalog_product = prod.id_catalog_product
  UNION ALL
  SELECT prod.sku, prod.id_catalog_product AS product_id, attr.name, attr.id_catalog_attribute, val.value::text, 'integer' FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_integer val ON val.fk_catalog_attribute = attr.id_catalog_attribute
  WHERE val.fk_catalog_product = prod.id_catalog_product
  UNION ALL
  SELECT prod.sku, prod.id_catalog_product, attr.name, attr.id_catalog_attribute, val.value::text, 'decimal' FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_decimal val ON val.fk_catalog_attribute = attr.id_catalog_attribute
  WHERE val.fk_catalog_product = prod.id_catalog_product
  UNION ALL
  SELECT prod.sku, prod.id_catalog_product, attr.name, attr.id_catalog_attribute, val.value::text, 'boolean' FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_boolean val ON val.fk_catalog_attribute = attr.id_catalog_attribute
  WHERE val.fk_catalog_product = prod.id_catalog_product
  UNION ALL
  SELECT prod.sku, prod.id_catalog_product, attr.name, attr.id_catalog_attribute, val.name::text, 'option_single' FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_option_single option_id ON option_id.fk_catalog_attribute = attr.id_catalog_attribute
    JOIN pac_catalog_value_option val ON option_id.fk_catalog_value_option = val.id_catalog_value_option
  WHERE option_id.fk_catalog_product = prod.id_catalog_product
  UNION ALL
  SELECT prod.sku, prod.id_catalog_product, attr.name, attr.id_catalog_attribute, string_agg(val.name, ' ---- ')::text, 'option_multi' FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_option_multi option_id ON option_id.fk_catalog_attribute = attr.id_catalog_attribute
    JOIN pac_catalog_value_option val ON option_id.fk_catalog_value_option = val.id_catalog_value_option
  WHERE option_id.fk_catalog_product = prod.id_catalog_product GROUP BY attr.id_catalog_attribute
  ) pof ON prod.id_catalog_product = pof.product_id
WHERE prod.sku = '{$sku}';
SQL;

    $result = pg_query($this->connection, $query);
    if ($result === false)
      throw new \Exception("Could not fetch product '{$sku}': " . pg_last_error());

    return $result;
  }

  function fetchCatalogProduct($sku)
  {
    $query = <<<QUERY
SELECT * FROM products WHERE sku='{$sku}';
QUERY;

    $result = pg_query($this->connection, $query);
    if ($result === false)
      throw new \Exception("Could not fetch product '{$sku}': " . pg_last_error());

    return $result;
  }
}
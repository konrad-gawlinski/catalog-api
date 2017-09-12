<?php

namespace Nu3\Core\Database\Gateway;

use Nu3\Core\Database\Exception;

class Product extends Base
{

  /**
   * @throws Exception
   */
  function create_product(string $sku, string $type, string $properties)
  {
    $result = pg_query_params($this->dbconn->con(), 'SELECT nu3__create_product_and_ct_node($1, $2, $3) as product_id;', [$sku, $type, $properties]);
    if (!$result) throw new Exception('Product could not be saved: '. pg_last_error($this->dbconn));

    $product_id = pg_fetch_result($result, 0, 'product_id');

    return intval($product_id);
  }

  /**
   * @throws Exception
   */
  function update_product(string $sku, string $properties)
  {
    $result = pg_query_params($this->dbconn->con(), 'SELECT nu3__update_product($1, $2);', [$sku, $properties]);
    if (!$result) throw new Exception('Product could not be updated: '. pg_last_error($this->dbconn));
  }


  /**
   * @throws Exception
   */
  function fetchProductBySku(string $sku, string $country, string $language) : array
  {
    $result = pg_query_params(
        $this->dbconn->con(),
        'SELECT * FROM nu3__fetch_product_merged($1, $2, $3);', [$sku, $country, $language]);
    if (!$result) throw new Exception('Product could not be updated: '. pg_last_error($this->dbconn));

    $result = pg_fetch_assoc($result);
    if (!$result) return [];

    return $result;
  }
}

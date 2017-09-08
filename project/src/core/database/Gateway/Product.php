<?php

namespace Nu3\Core\Database\Gateway;

use Nu3\Core\Database\Exception;

class Product extends Base
{

  /**
   * @param string $sku
   * @param string $attributes
   * @throws Exception
   */
  function save_product(string $sku, string $attributes)
  {
    $result = pg_query_params($this->dbconn->con(), 'SELECT save_product($1, $2);', [$sku, $attributes]);
    if (!$result) throw new Exception('Product could not be saved: '. pg_last_error($this->dbconn));
  }

  function fetchProductBySku(string $sku, string $country, string $language) : array
  {
    $result = pg_query_params(
        $this->dbconn->con(),
        'SELECT * FROM nu3__fetch_product_merged($1, $2, $3);', [$sku, $country, $language]);
    $result = pg_fetch_assoc($result);

    if ($result === false) return [];

    return $result;
  }
}

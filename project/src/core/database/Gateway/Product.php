<?php

namespace Nu3\Core\Database\Gateway;

use Nu3\Core\Database\Exception;

class Product extends Base
{
  /**
   * @throws Exception
   */
  function save_product(string $sku, string $status, string $properties)
  {
    $result = pg_query_params($this->dbconn->db(), 'SELECT catalog.save_product($1, $2, $3);', [$sku, $status, $properties]);
    if (!$result) throw new Exception('Product could not be saved: '. pg_last_error($this->dbconn));
  }

  function fetchProductType(string $sku) : array
  {
    $result = pg_query_params($this->dbconn->db(), 'SELECT * FROM catalog.fetch_product_type($1);', [$sku]);
    $result = pg_fetch_assoc($result);

    if ($result === false) return [];

    return $result;
  }
}
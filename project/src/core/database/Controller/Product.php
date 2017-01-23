<?php

namespace Nu3\Core\Database\Controller;

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

  function fetch_product(string $sku) : array
  {
    $result = pg_query_params($this->dbconn->db(), 'SELECT catalog.fetch_product($1);', [$sku]);
    $result = pg_fetch_row($result);
    $value = reset($result);

    if (!$value) return [];

    return json_decode($value, true);
  }
}
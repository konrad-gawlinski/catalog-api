<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Product_service extends \Codeception\Module
{
  function removeProductBySku($con, string $sku)
  {
    $error = false;

    $result = pg_query(
      $con,
      "DELETE FROM product_relations r USING products p WHERE p.id = r.parent_id AND p.sku = '{$sku}'"
    );
    if ($result === false) $error = "Could not remove product_entity relation\n";

    $result = pg_query(
      $con,
      "DELETE FROM products WHERE sku = '{$sku}'"
    );
    if ($result === false) $error = "Could not remove product_entity\n";

    if ($error) echo "\033[0;31m". $error ."\033[0m";
  }
}

<?php

namespace Nu3\ProductMigration\Migrator;

class ProductRelations
{
  use Property\Database;

  function __construct($dbCon)
  {
    $this->setDbCon($dbCon);
  }

  function initializeProductRelations()
  {
    $products = pg_query($this->dbCon, "SELECT id FROM catalog.product_entity");

    while ($row = pg_fetch_assoc($products)) {
      pg_query($this->dbCon, "SELECT nu3__ct_create_node('{$row['id']}');");
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
      pg_query($this->dbCon, "SELECT nu3__ct_make_node_a_child({$row['bundle_entity']}, {$row['config_entity']}, 1);");
    }
  }
}

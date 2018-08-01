<?php

namespace Nu3\Core\Database\Gateway;

use Nu3\Core\Database\Connection;
use Nu3\Core\Database\QueryBuilder;

class Product extends Base
{
  /** @var QueryBuilder */
  private $queryBuilder;

  function __construct(Connection $dbConnection)
  {
    parent::__construct($dbConnection);

    $this->queryBuilder = new QueryBuilder();
  }

  function productExists(string $sku) : bool
  {
    $_sku = pg_escape_literal($sku);

    $result = pg_query($this->dbConnection->connectionRes(),
      "SELECT count(*) FROM products WHERE sku={$_sku}"
    );

    $skuExists = pg_fetch_row($result)[0] === '1';
    return $skuExists;
  }

  /**
   * @throws \Exception
   */
  function createProduct(?string $sku, string $type, array $properties) : int
  {
    $queryColumns = 'sku,type';
    $_sku = is_null($sku) ? 'null' : pg_escape_literal($sku);
    $queryValues =  $_sku .','. pg_escape_literal($type);
    list($columns, $values) = $this->queryBuilder->concatColumnsAndJsonValues($queryColumns, $queryValues, $properties);

    $result = pg_query($this->dbConnection->connectionRes(),
      "INSERT INTO products ({$columns}) VALUES ({$values}) RETURNING id"
    );

    $productId = pg_fetch_row($result)[0];
    return intval($productId);
  }

  /**
   * @throws \Exception
   */
  function updateProduct(int $id, array $properties) : int
  {
    $querySetStatement = $this->queryBuilder->buildJsonMergeUpdateList($properties);

    $query = <<<QUERY
WITH update_statement AS (
  UPDATE products SET {$querySetStatement} WHERE id={$id} RETURNING *
)
SELECT count(*) FROM update_statement;
QUERY;
    $result = pg_query($this->dbConnection->connectionRes(), $query);

    $totalAffectedRows = pg_fetch_row($result)[0];
    return intval($totalAffectedRows);
  }

  /**
   * @throws \Exception
   */
  function createNode(int $productId)
  {
    pg_query($this->dbConnection->connectionRes(),
      "INSERT INTO product_relations VALUES ({$productId}, {$productId}, 0)"
    );
  }

  function addToRelationBranch(int $childId, array $branchParentIds) : int
  {
    if (!$branchParentIds) return 0;

      $queryValues = $this->queryBuilder->prepareForValuesExpression($branchParentIds);

      $query = <<<QUERY
WITH insert_statement AS (
  INSERT INTO product_relations
    SELECT parents.id as parent_id, product.id as child_id, row_number(*) OVER () as depth FROM
    (values{$queryValues}) AS parents(id) CROSS JOIN (values ({$childId})) as product(id)
  RETURNING *
)
SELECT count(*) FROM insert_statement
QUERY;

      $result = pg_query($this->dbConnection->connectionRes(), $query);
      $totalInsertedRows = intval(pg_fetch_row($result)[0]);

      return $totalInsertedRows;
  }

  function fetchRawProductById(int $productId) : array
  {
    $result = pg_query($this->dbConnection->connectionRes(),
      "SELECT * FROM products WHERE id={$productId}"
    );

    $row = pg_fetch_assoc($result);
    return $row ?: [];
  }

  /**
   * @param array $regionPairs comma separated list of region pairs e.g. 'de,de_de,com'
   * @return array
   */
  function fetchProductById(int $productId, array $regionPairs) : array
  {
    $regionsMergeColumns = $this->queryBuilder->buildRegionMergeColumns($regionPairs);
    $query = <<<QUERY
SELECT parent_id as id, sku, type, {$regionsMergeColumns[0]} as properties FROM (
  SELECT parent_id,
   (array_agg(sku ORDER BY depth ASC))[1] as sku,
   (array_agg(type ORDER BY depth ASC))[1] as type,
   jsonb_merge(global ORDER BY depth DESC) as global,
   {$regionsMergeColumns[1]}
   
FROM product_relations JOIN products ON child_id = id
WHERE parent_id = {$productId} AND (sku IS NULL OR depth = 0)
GROUP BY parent_id
) AS product;
QUERY;

    $result = pg_query($this->dbConnection->connectionRes(), $query);

    $row = pg_fetch_assoc($result);
    return $row ?: [];
  }
}

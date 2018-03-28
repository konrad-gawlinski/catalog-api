<?php

namespace Nu3\Core\Database\Gateway;

use Nu3\Core\Database\Exception as DatabaseException;
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

  function productExists(string $sku)
  {
    return $this->runQueryFunction(
      function() use ($sku) {
        return $this->queryProductExists($sku);
      },
      'Product existence could not be verified'
    );
  }

  private function queryProductExists(string $sku) : bool
  {
    $_sku = pg_escape_literal($sku);

    $result = pg_query($this->dbConnection->connectionRes(),
      "SELECT count(*) FROM products WHERE sku={$_sku}"
    );

    $skuExists = pg_fetch_row($result)[0] === '1';
    return $skuExists;
  }

  /**
   * @throws DatabaseException
   */
  function createProduct(?string $sku, string $type, array $properties) : int
  {
    return $this->runQueryFunction(
      function() use ($sku, $type, $properties) {
        return $this->queryCreateProduct($sku, $type, $properties);
      },
      'Product could not be created'
    );
  }

  /**
   * @throws \Exception
   */
  private function queryCreateProduct(?string $sku, string $type, array $properties) : int
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
   * @throws DatabaseException
   */
  public function createNode(int $productId) {
    return $this->runQueryFunction(
      function() use ($productId) {
        $this->queryCreateNode($productId);
      },
      'Product node could not be created'
    );
  }

  /**
   * @throws \Exception
   */
  private function queryCreateNode(int $productId)
  {
    pg_query($this->dbConnection->connectionRes(),
      "INSERT INTO product_relations VALUES ({$productId}, {$productId}, 0)"
    );
  }

  /**
   * @throws DatabaseException
   */
  public function addAddToRelationBranch(int $childId, array $branchParentIds) : int
  {
    return $this->runQueryFunction(
      function() use ($childId, $branchParentIds) {
        return $this->queryAddToRelationBranch($childId, $branchParentIds);
      },
      'Relation could not be created'
    );
  }
  
  private function queryAddToRelationBranch(int $childId, array $branchParentIds) : int
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
}

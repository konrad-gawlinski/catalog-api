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

  /**
   * @throws DatabaseException
   */
  function create_product(string $sku, string $type, array $properties) : int
  {
    return $this->run_query_function(
      function() use ($sku, $type, $properties) {
        return $this->query_create_product($sku, $type, $properties);
      },
      'Product could not be created'
    );
  }

  /**
   * @throws \Exception
   */
  private function query_create_product(string $sku, string $type, array $properties) : int
  {
    $queryColumns = 'sku,type';
    $queryValues = pg_escape_literal($sku) .','. pg_escape_literal($type);
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
  public function create_node(int $productId) {
    return $this->run_query_function(
      function() use ($productId) {
        $this->query_create_node($productId);
      },
      'Product node could not be created'
    );
  }

  /**
   * @throws \Exception
   */
  private function query_create_node(int $productId)
  {
    pg_query($this->dbConnection->connectionRes(),
      "INSERT INTO product_relations VALUES ({$productId}, {$productId}, 0)"
    );
  }

  /**
   * @throws DatabaseException
   */
  private function run_query_function(callable $queryFunction, string $errorMsg)
  {
    try {
      return $queryFunction();
    } catch(\Exception $e) {
      $lastError = pg_last_error($this->dbConnection->connectionRes());
      throw new DatabaseException("{$errorMsg}: ". $lastError, 0, $e);
    }
  }
}

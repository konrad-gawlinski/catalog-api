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
  function create_product(string $sku, string $type, array $properties) : string
  {
    try {
      return $this->query_create_product($sku, $type, $properties);
    } catch(\Exception $e) {
      throw new DatabaseException(
        'Product could not be created: '. pg_last_error($this->dbConnection->connectionRes()),
        0, $e);
    }
  }

  /**
   * @throws \Exception
   */
  private function query_create_product(string $sku, string $type, array $properties) : string
  {
    $queryColumns = 'sku,type';
    $queryValues = pg_escape_literal($sku) .','. pg_escape_literal($type);
    list($columns, $values) = $this->queryBuilder->concatColumnsAndJsonValues($queryColumns, $queryValues, $properties);

    $result = pg_query($this->dbConnection->connectionRes(),
      "INSERT INTO products ({$columns}) VALUES ({$values}) RETURNING id"
    );

    $productId = pg_fetch_row($result)[0];
    return $productId;
  }
}

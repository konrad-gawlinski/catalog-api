<?php

namespace Nu3\Core\Database\Gateway;

use Nu3\Core\Database\Exception;
use Nu3\Core\Database\Connection;
use Nu3\Core\Database\QueryBuilder;

class Product extends Base
{
  /** @var QueryBuilder */
  private $queryBuilder;

  function __construct(Connection $dbconn)
  {
    parent::__construct($dbconn);

    $this->queryBuilder = new QueryBuilder();
  }

  function create_product(string $sku, string $type, array $properties)
  {
    $queryColumns = 'sku,type';
    $queryValues = pg_escape_literal($sku) .','. pg_escape_literal($type);
    list($columns, $values) = $this->queryBuilder->concatColumnsAndJsonValues($queryColumns, $queryValues, $properties);

    $result = pg_query($this->dbconn->con(),
      "INSERT INTO product ({$columns}) VALUES ({$values}) RETURNING id"
    );
    if (!$result) throw new Exception('Product could not be created: '. pg_last_error($this->dbconn));
  }
}

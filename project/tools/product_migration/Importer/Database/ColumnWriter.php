<?php

namespace Nu3\ProductMigration\Importer\Database;

class ColumnWriter
{
  private $con;

  function __construct($dbConnection)
  {
    $this->con = $dbConnection;
  }

  public function write(string $tableName, array $columns)
  {
    $columnNames = implode(',', array_keys($columns));
    $values = $this->prepareValues($columns);

    pg_query($this->con,
      "INSERT INTO {$tableName} ({$columnNames}) VALUES({$values});"
    );
  }

  private function prepareValues(array $columns)
  {
    $values = '';

    foreach ($columns as $column) {
      $_value = $column['value'];
      if ($column['type'] === 'text') {
        $_value = addcslashes($_value, "'");
        $_value = "'{$_value}'";
      }

      $values .= "{$_value},";
    }

    return rtrim($values, ',');
  }
}
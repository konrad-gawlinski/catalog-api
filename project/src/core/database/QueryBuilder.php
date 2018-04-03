<?php

namespace Nu3\Core\Database;

class QueryBuilder
{
  function concatColumnsAndJsonValues(string $csvColumns, string $csvValues, array $properties) : array
  {
    list($columns, $values) = $this->separateColumnsFromJsonValues($properties);
    $queryColumns = $csvColumns;
    $queryValues = $csvValues;

    if ($queryColumns && $columns) $queryColumns .= ','. $columns;
    else if ($columns) $queryColumns = $columns;

    if ($queryValues && $values) $queryValues .= ','. $values;
    else if ($values) $queryValues = $values;

    return [$queryColumns, $queryValues];
  }

  private function separateColumnsFromJsonValues(array $properties) : array
  {
    $columns = '';
    $values = '';
    $comma = '';

    foreach ($properties as $column => $value) {
      $columns .= $comma . pg_escape_identifier($column);
      $values .= $comma . pg_escape_literal(json_encode($value));
      $comma = ',';
    }

    return [$columns, $values];
  }

  function prepareForValuesExpression(array $values) : string
  {
    $result = '';
    $comma = '';
    foreach ($values as $id) {
      $result .= $comma ."({$id})";
      $comma = ',';
    }

    return $result;
  }

  function buildJsonMergeUpdateList(array $properties)
  {
    $queryList = '';
    $comma = '';

    foreach ($properties as $region => $value) {
      $column = pg_escape_identifier($region);
      $encodedValue = pg_escape_literal(json_encode($value));
      $queryList .= $comma . "{$column}={$column} || {$encodedValue}";
      $comma = ',';
    }

    return $queryList;
  }
}
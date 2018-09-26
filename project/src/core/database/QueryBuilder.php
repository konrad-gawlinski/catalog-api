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

  function buildJsonMergeUpdateList(array $properties) : string
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

  /**
   * @param array $regionPairs region pairs e.g. ['de,de_de' ,com,en_gb']
   * @return array
   */
  function buildRegionMergeColumns(array $regionPairs) : array
  {
    $selectStatements = $this->buildRegionMergeColumnsSelectStatement($regionPairs);
    $queryStatements = $this->buildRegionMergeColumnsQueryStatement($regionPairs);

    return [
      implode(', ', $selectStatements),
      implode(', ', $queryStatements)
    ];
  }

  /**
   * @return array e.g. ['global || de || de_de as de-de_de', 'global || com || en_gb as com-en_gb']
   */
  private function buildRegionMergeColumnsSelectStatement(array $regionPairs) : array
  {
    return array_map(function($regionPairCsv) {
      list($country, $language) = explode(',', $regionPairCsv);

      return "global || {$country} || {$language} as \"{$country}-{$language}\"";
    }, $regionPairs);
  }

  private function buildRegionMergeColumnsQueryStatement(array $regionPairs) : array
  {
    $allRegions = explode(',', implode(',', $regionPairs));
    $uniqueRegions = array_unique(explode(',', implode(',', $allRegions)));

    return array_map(function($region) {
      return "jsonb_merge({$region} ORDER BY depth DESC) as {$region}";
    }, $uniqueRegions);
  }
}

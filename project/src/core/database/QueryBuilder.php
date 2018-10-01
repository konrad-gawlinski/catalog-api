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
   * @param array $regions e.g. ['global', 'de', 'en_gb', 'fr']
   */
  function buildProductQueryPartsByRegions(array $regions) : array
  {
    $allRegions = array_merge(['global'], $regions);
    $selectStatements = implode(',', $allRegions);
    $queryStatements = $this->buildProductMergeColumnsQueryStatement($allRegions);

    return [
      $selectStatements,
      implode(', ', $queryStatements)
    ];
  }

  /**
   * @param array $regionPairs e.g. [['de','de_de'],['com','en_gb']]
   * @return array
   */
  function buildProductQueryPartsByRegionPairs(array $regionPairs) : array
  {
    $selectStatements = $this->buildProductMergeRegionsSelectionColumns($regionPairs);
    $uniqueRegions = array_merge(['global'], $this->extractUniqueRegionsFromRegionPairs($regionPairs));
    $queryStatements = $this->buildProductMergeColumnsQueryStatement($uniqueRegions);

    return [
      implode(', ', $selectStatements),
      implode(', ', $queryStatements)
    ];
  }

  /**
   * @param array $regionPairs e.g. [['de','de_de'],['com','en_gb']]
   * @return array e.g. ['global || de || de_de as de-de_de', 'global || com || en_gb as com-en_gb']
   */
  private function buildProductMergeRegionsSelectionColumns(array $regionPairs) : array
  {
    return array_map(function($regionPair) {
      list($country, $language) = $regionPair;

      return "global || {$country} || {$language} as \"{$country}-{$language}\"";
    }, $regionPairs);
  }

  /**
   * @param array $regionPairs e.g. [['de','de_de'],['com','en_gb']]
   */
  private function extractUniqueRegionsFromRegionPairs(array $regionPairs) : array
  {
    $regionPairsCsv = array_map(function($regionPair) {
      return "{$regionPair[0]},{$regionPair[1]}";
    }, $regionPairs);
    $allRegions = explode(',', implode(',', $regionPairsCsv));
    return array_unique($allRegions);
  }

  /**
   * @param array $regions e.g. ['de', 'de_de', 'com', 'en_gb']
   */
  private function buildProductMergeColumnsQueryStatement(array $regions) : array
  {
    return array_map(function($region) {
      $trimmedRegion = trim($region);
      return "jsonb_merge({$trimmedRegion} ORDER BY depth DESC) as {$trimmedRegion}";
    }, $regions);
  }
}

<?php

namespace Kwg\Filter;

use Kwg\Filter\Exception;

class MetaData
{
  /** @var string */
  private $propertyName;
  /** @var array */
  private $filters;
  /** @var MetaData[] */
  private $children = [];

  function __construct(string $propertyName, array $filters = [])
  {
    $this->propertyName = $propertyName;
    $this->filters = $filters;
  }

  function getPropertyName() : string
  {
    return $this->propertyName;
  }

  function addFilter(array $filter)
  {
    if (!isset($filter[0]))
      throw new Exception\NoFilter();

    $this->filters[] = $filter;
  }

  function getFilters() : array
  {
    return $this->filters;
  }

  function addChild(MetaData $child)
  {
    $this->children[] = $child;
  }

  /**
   * @return MetaData[]
   */
  function getChildren() : array
  {
    return $this->children;
  }
}
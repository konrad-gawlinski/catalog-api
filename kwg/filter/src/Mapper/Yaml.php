<?php

namespace Kwg\Filter\Mapper;

use Symfony\Component\Yaml\Yaml as SymfonyYaml;
use Kwg\Filter\MetaData;

class Yaml implements Mapper
{
  /** @var string */
  private $file;

  function __construct(string $file)
  {
    $this->file = $file;
  }

  function normalize() : MetaData
  {
    $value = SymfonyYaml::parse(file_get_contents($this->file));
    print_r($value);

    $metaData = new MetaData('root');
    $entity = reset($value);

    if (is_array($entity[self::PROPERTY_PROPERTIES]))
      $metaData = $this->readProperties($metaData, $entity[self::PROPERTY_PROPERTIES]);

    return $metaData;
  }

  private function readProperties(MetaData $parent, array $entity)
  {
    foreach($entity as $property => $filters) {
        $_filters = array_map(function($filter) {
          return (array)$filter;
        }, $filters);

        $parent->addChild(new MetaData($property, $_filters));
      }

    return $parent;
  }
}

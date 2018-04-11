<?php
namespace Nu3\Feature;

use Nu3\Service\Product\PropertyMap as PropertyMapObject;

trait PropertyMap
{
  private $propertyMap;

  function setPropertyMap(PropertyMapObject $propertyMap)
  {
    $this->propertyMap = $propertyMap;
  }

  protected function propertyMap() : PropertyMapObject
  {
    return $this->propertyMap;
  }
}
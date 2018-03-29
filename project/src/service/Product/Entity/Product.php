<?php

namespace Nu3\Service\Product\Entity;

class Product
{
  const ID = 'id';
  const SKU = 'sku';
  const TYPE = 'type';
  const PROPERTIES = 'properties';

  public $id;
  public $sku = '';
  public $type = '';
  public $properties = [];

  function fillFromDb(array $input)
  {
    $this->id = $input[self::ID];
    $this->sku = $input[self::SKU];
    $this->type = $input[self::TYPE];
    foreach ($this->getRegionNames() as $region) {
      $this->properties[$region] = json_decode($input[$region], true);
    }
  }

  private function getRegionNames()
  {
    return ['global', 'de', 'fr', 'at', 'de_de', 'fr_fr', 'at_de'];
  }
}

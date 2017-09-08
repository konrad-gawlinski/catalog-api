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
    $this->properties = json_decode($input[self::PROPERTIES], true);
  }
}

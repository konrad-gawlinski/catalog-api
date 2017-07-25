<?php

namespace Nu3\Service\Product\Entity;

class Product
{
  const SKU = 'sku';
  const ATTRIBUTES = 'attributes';

  public $sku = '';
  public $attributes = [];

  function fillFromDb(array $input)
  {
    $this->sku = $input[self::SKU];
    $this->attributes = json_decode($input[self::ATTRIBUTES], true);
  }
}

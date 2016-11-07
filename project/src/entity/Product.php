<?php
namespace Nu3\Entity;

class Product
{
  public $sku = '';
  public $name = '';
  public $price = -1;
  public $taxRate = -1;
  public $attributes = [];
  public $seoRobots = [];
  public $manufacturer = '';
  public $labelLanguage = [];
  public $ingredientList = -1;
}
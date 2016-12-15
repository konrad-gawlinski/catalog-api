<?php

namespace Nu3\Service\Product\Entity;

use JMS\Serializer\Annotation\Type;

class Product
{
  /**
   * @Type("string")
   */
  public $sku = '';

  /**
   * @Type("string")
   */
  public $name = '';

  /**
   * @Type("string")
   */
  public $type = '';

  /**
   * @Type("Nu3\Service\Product\Entity\Price")
   */
  public $price;

  /**
   * @Type("integer")
   */
  public $taxRate = 0;

  /**
   * @Type("array<string>")
   */
  public $attributes = [];

  /**
   * @Type("Nu3\Service\Product\Entity\Seo")
   */
  public $seo;

  /**
   * @Type("string")
   */
  public $manufacturer = '';

  /**
   * @Type("array<string>")
   */
  public $labelLanguage = [];
}
<?php

namespace Nu3\Service\Product\Entity;

use JMS\Serializer\Annotation\Type;

class Payload
{
  /**
   * @Type("Nu3\Service\Product\Entity\Product")
   */
  public $product;

  /**
   * @Type("string")
   */
  public $storage;
}
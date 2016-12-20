<?php

namespace Nu3\Service\Product\Entity\Property;

use JMS\Serializer\Annotation as Serializer;

class Price
{
  /**
   * @Serializer\Type("integer")
   */
  public $final = 0;
}
<?php

namespace Nu3\Service\Product\Entity;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Payload implements \Nu3\Core\Entity
{
  /**
   * @Serializer\Exclude
   * @Assert\Valid()
   */
  public $product;

  /**
   * @Serializer\Type("string")
   */
  public $storage;
}
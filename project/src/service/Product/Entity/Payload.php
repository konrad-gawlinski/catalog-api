<?php

namespace Nu3\Service\Product\Entity;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Payload implements \Nu3\Core\Payload
{
  /**
   * @Serializer\Type("Nu3\Service\Product\Entity\Product")
   * @Assert\Valid()
   */
  public $product;

  /**
   * @Serializer\Type("string")
   */
  public $storage;
}
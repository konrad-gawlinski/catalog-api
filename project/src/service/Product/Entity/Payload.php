<?php

namespace Nu3\Service\Product\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Payload
{
  public $product = [];
  public $storage = '';
}
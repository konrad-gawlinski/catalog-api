<?php

namespace Nu3\Service\Product;

use Symfony\Component\Validator\Constraints as Assert;

class Entity
{
  /**
   * @Assert\NotBlank()
   */
  public $sku = '';

  /**
   * @Assert\Collection(
   *     fields = {
   *         "name" = @Assert\NotBlank(),
   *     },
   *     allowExtraFields = true,
   *     allowMissingFields = true
   * )
   */
  public $properties = [];
}
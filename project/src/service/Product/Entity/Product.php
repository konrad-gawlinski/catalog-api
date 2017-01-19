<?php

namespace Nu3\Service\Product\Entity;

use JMS\Serializer\Annotation as Serializer;
use DMS\Filter\Rules as Filter;
use Symfony\Component\Validator\Constraints as Assert;

class Product implements \Nu3\Core\Entity
{
  /**
   * @Serializer\Type("string")
   * @Filter\Trim()
   * @Assert\NotBlank()
   */
  public $sku = '';

  /**
   * @Serializer\Type("string")
   * @Filter\Trim()
   * @Assert\Choice(choices = {"new", "approved", "not listed", "unavailable"})
   */
  public $status = '';

  /**
   * @Serializer\Type("string")
   * @Filter\Trim()
   * @Assert\NotBlank()
   */
  public $name = '';

  /**
   * @Serializer\Type("string")
   * @Filter\Trim()
   * @Assert\NotBlank()
   */
  public $type = '';

  /**
   * @Serializer\Type("Nu3\Service\Product\Entity\Property\Price")
   * @Assert\Valid()
   */
  public $price;

  /**
   * @Serializer\Type("integer")
   * @Assert\Range(
   *      min = 0,
   *      max = 100
   * )
   */
  public $taxRate = 0;

  /**
   * @Serializer\Type("array<string>")
   */
  public $attributes = [];

  /**
   * @Serializer\Type("Nu3\Service\Product\Entity\Property\Seo")
   * @Assert\Valid()
   */
  public $seo;

  /**
   * @Serializer\Type("string")
   * @Filter\Trim()
   * @Assert\NotBlank()
   */
  public $manufacturer = '';

  /**
   * @Serializer\Type("string")
   * @Filter\Trim()
   */
  public $description = '';

  /**
   * @Serializer\Type("string")
   * @Filter\Trim()
   */
  public $shortDescription = '';

  /**
   * @Serializer\Type("array<string>")
   */
  public $labelLanguage = [];
}
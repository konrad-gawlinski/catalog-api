<?php

namespace Nu3\Service\Product\Entity;

use JMS\Serializer\Annotation as Serializer;
use Dms\Filter\Rules as Filter;
use Symfony\Component\Validator\Constraints as Assert;

class Seo
{
  /**
   * @Serializer\Type("array<string>")
   * @Assert\Choice(
   *   choices = {"noindex", "index", "nofollow", "follow"},
   *   multiple = true
   * )
   */
  public $robots = ['noindex', 'nofollow'];

  /**
   * @Serializer\Type("string")
   * @Filter\Trim()
   *
   * @Assert\NotBlank()
   */
  public $title = '';

  /**
   * @Serializer\Type("string")
   * @Filter\Trim()
   */
  public $description = '';
}
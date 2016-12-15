<?php

namespace Nu3\Service\Product\Entity;

use JMS\Serializer\Annotation\Type;

class Seo
{
  /**
   * @Type("array<string>")
   */
  public $robots = ['noindex', 'nofollow'];

  /**
   * @Type("string")
   */
  public $title = '';

  /**
   * @Type("string")
   */
  public $description = '';
}
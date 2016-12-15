<?php

namespace Nu3\Service\Product;

use JMS\Serializer\Serializer as ExternalSerializer;
use Nu3\Service\Product\Entity\Payload as Payload;

class Serializer
{
  /** @var  ExternalSerializer */
  private $serializer;

  function __construct(ExternalSerializer $serializer)
  {
    $this->serializer = $serializer;
  }

  function deserialize(string $json) : Payload
  {
    $payload = $this->serializer->deserialize($json, Payload::class, 'json');

    return $payload;
  }
}
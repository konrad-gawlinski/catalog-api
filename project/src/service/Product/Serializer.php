<?php

namespace Nu3\Service\Product;

use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Nu3\Service\Product\Entity as ProductEntity;

class Serializer
{
  /** @var  SymfonySerializer */
  private $serializer;
  /** @var \JsonSchema\Validator */
  private $schemaValidator;

  function __construct(SymfonySerializer $serializer, \JsonSchema\Validator $schemaValidator)
  {
    $this->serializer = $serializer;
    $this->schemaValidator = $schemaValidator;
  }

  function deserialize(string $json) : ProductEntity
  {
    $this->schemaValidator->check(json_decode($json), $this->getSchema());
    $product = $this->serializer->deserialize($json, ProductEntity::class, 'json');
    var_dump('Json Errors: ', $this->schemaValidator->getErrors());
    
    return $product;
  }

  private function getSchema() : string
  {
    return file_get_contents(__DIR__ . '/config/validation-schema.json');
  }
}
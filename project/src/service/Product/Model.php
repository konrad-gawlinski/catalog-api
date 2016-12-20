<?php

namespace Nu3\Service\Product;

use Nu3\Core;
use DMS\Filter\Filter;
use Nu3\Service\Product\Entity as ProductEntity;
use Symfony\Component\Validator\ConstraintViolation;

class Model
{
  /** @var string */
  private $sku;

  /** @var string */
  private $type;

  /** @var Core\JsonValidator */
  private $jsonValidator;

  /** @var Core\Serializer */
  private $serializer;

  /** @var Core\Validator */
  private $validator;

  /** @var Filter */
  private $sanitizer;

  function set(string $sku, string $type)
  {
    $this->sku = $sku;
    $this->type = $type;
  }

  function setJsonValidator(Core\JsonValidator $jsonValidator)
  {
    $this->jsonValidator = $jsonValidator;
  }

  function setSerializer(Core\Serializer $serializer)
  {
    $this->serializer = $serializer;
  }

  function setValidator(Core\Validator $validator)
  {
    $this->validator = $validator;
  }

  function setSanitizer(Filter $sanitizer)
  {
    $this->sanitizer = $sanitizer;
  }

  function validateSchema(array $data)
  {
    $schema = $this->chooseSchema();
    if ($schema === '') {
      //Todo: create violations
    }

    $this->jsonValidator->validate($data, $schema);
  }

  private function chooseSchema() : string
  {
    switch ($this->type) {
      case 'config': return APPLICATION_SRC . 'service/Product/config/validation-schema.json';
    }

    return '';
  }

  function deserialize(string $json, array $productData) : ProductEntity\Payload
  {
    $productClass = $this->chooseProductClass();
    if ($productClass === '') {
      //Todo create violations
    }
    /** @var ProductEntity\Payload $payload */
    $payload = $this->serializer->deserialize($json, ProductEntity\Payload::class);
    $payload->product = $this->serializer->deserialize(json_encode($productData), $productClass);
    $this->sanitize($payload);

    return $payload;
  }

  private function chooseProductClass() : string
  {
    switch ($this->type) {
      case 'config': return ProductEntity\Config::class;
    }

    return '';
  }

  private function sanitize(ProductEntity\Payload $payload)
  {
    $this->sanitizer->filterEntity($payload->product);
    $this->sanitizer->filterEntity($payload->product->seo);
    $this->sanitizer->filterEntity($payload->product->price);
  }

  function validate(ProductEntity\Payload $payload)
  {
    $violations = $this->validator->validate($payload);

    /** @var ConstraintViolation $violation */
    foreach ($violations as $violation) {
      var_dump($violation->getMessage());
    }
  }
}
<?php

namespace Nu3\Service\Product;

class JsonValidator
{
  /** @var \JsonSchema\Validator */
  private $schemaValidator;

  function __construct(\JsonSchema\Validator $schemaValidator)
  {
    $this->schemaValidator = $schemaValidator;
  }

  function validate(string $json)
  {
    $data = json_decode($json);
    $this->schemaValidator->check($data, $this->getSchema());

    var_dump('Json Errors: ', $this->schemaValidator->getErrors());
  }

  private function getSchema() : string
  {
    return file_get_contents(__DIR__ . '/config/validation-schema.json');
  }
}
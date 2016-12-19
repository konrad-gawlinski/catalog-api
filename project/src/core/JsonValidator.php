<?php

namespace Nu3\Core;

class JsonValidator
{
  /** @var \JsonSchema\Validator */
  private $schemaValidator;

  function __construct(\JsonSchema\Validator $schemaValidator)
  {
    $this->schemaValidator = $schemaValidator;
  }

  function validate(string $json, string $schemaPath)
  {
    $data = json_decode($json);
    $this->schemaValidator->check($data, $this->getSchema($schemaPath));

    var_dump('Json Errors: ', $this->schemaValidator->getErrors());
  }

  private function getSchema($schemaPath) : string
  {
    return file_get_contents($schemaPath);
  }
}
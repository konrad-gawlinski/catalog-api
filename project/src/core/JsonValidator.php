<?php

namespace Nu3\Core;

class JsonValidator
{
  protected function buildSchemaValidator()
  {
    return new \JsonSchema\Validator();
  }

  function validate(array $data, string $schemaPath)
  {
    $schemaValidator = $this->buildSchemaValidator();
    $schemaValidator->check($data, $this->getSchema($schemaPath));

    var_dump('Json Errors: ', $schemaValidator->getErrors());
  }

  private function getSchema($schemaPath) : string
  {
    return file_get_contents($schemaPath);
  }
}
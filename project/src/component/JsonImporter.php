<?php
namespace Nu3\Component;

use \JsonSchema\Validator as SchemaValidator;

class JsonImporter
{
  private $schemaValidator;

  function import(string $payload)
  {

  }

  function setSchemaValidator(SchemaValidator $schemaValidator)
  {
    $this->schemaValidator = $schemaValidator;
  }
}
<?php

namespace Nu3\Service\Product;

use Nu3\Core;
use Nu3\Service\Product\Entity as ProductEntity;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\ConstraintViolation;
use Nu3\Core\Database\Broker\Factory as DbFactory;
use Nu3\Service\Product\Entity\Properties as ProductProperty;

class Model
{
  /** @var string */
  private $sku;

  /** @var string */
  private $type;

  /** @var Core\JsonValidator */
  private $jsonValidator;

  /** @var Core\Validator */
  private $validator;

  /** @var  DbFactory */
  private $dbFactory;

  function set(string $sku, string $type)
  {
    $this->sku = $sku;
    $this->type = $type;
  }

  function setJsonValidator(Core\JsonValidator $jsonValidator)
  {
    $this->jsonValidator = $jsonValidator;
  }

  function setValidator(Core\Validator $validator)
  {
    $this->validator = $validator;
  }

  function setDbFactory(DbFactory $factory)
  {
    $this->dbFactory = $factory;
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
      case 'config': return APPLICATION_SRC . 'service/Product/config/validation/rest-request/payload.json';
    }

    throw new Exception('Unknown REST request validation rules file');
  }

  function validate(ProductEntity\Payload $payload)
  {
    $violations = $this->validator
      ->buildValidator($this->chooseValidationRules())
      ->validate($payload);

    /** @var ConstraintViolation $violation */
    foreach ($violations as $violation) {
      var_dump($violation);
    }
  }

  private function chooseValidationRules() : string
  {
    switch ($this->type) {
      case 'config': return APPLICATION_SRC . 'service/Product/config/validation/entity/payload.yml';
    }

    throw new Exception('Unknown product validation rules file');
  }

  function prepareProductForDb(array $product) : string
  {
    unset($product[ProductProperty::PRODUCT_SKU]);
    unset($product[ProductProperty::PRODUCT_STATUS]);

    return json_encode($product);
  }

  function getDatabaseProductBroker() : Core\Database\Broker\Product
  {
    return $this->dbFactory->getProductBroker();
  }
}
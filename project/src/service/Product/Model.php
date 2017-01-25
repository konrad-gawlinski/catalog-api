<?php

namespace Nu3\Service\Product;

use Nu3\Core;
use Nu3\Service\Product\Entity as ProductEntity;
use Nu3\Core\Database\Controller\Factory as DbFactory;
use Nu3\Service\Product\Entity\Properties;

class Model
{
  /** @var PayloadValidator */
  private $payloadValidator;

  /** @var EntityValidator */
  private $validator;

  /** @var  DbFactory */
  private $dbFactory;

  function setPayloadValidator(PayloadValidator $payloadValidator)
  {
    $this->payloadValidator = $payloadValidator;
  }

  function setEntityValidator(EntityValidator $validator)
  {
    $this->validator = $validator;
  }

  function setDbFactory(DbFactory $factory)
  {
    $this->dbFactory = $factory;
  }

  function validatePayload(array $data)
  {
    $this->payloadValidator->validatePayload($data);
  }

  function validateEntity(ProductEntity\Product $product)
  {
    $this->validator->validate($product);
  }

  function createProductEntity(ProductEntity\Payload $payload) : ProductEntity\Product
  {
    $db = $this->dbFactory->getProductController();
    $db->set_schema($this->chooseDbSchema($payload->storage));
    $result = $db->fetch_product($payload->product[Properties::PRODUCT_SKU]);
    $product = $this->initializeProductEntity($result, $payload);

    $product->properties = array_replace_recursive(
      $this->fetchDefaultValues($product->properties[Properties::PRODUCT_TYPE]),
      $payload->product,
      $product->properties
    );

    return $product;
  }

  private function initializeProductEntity(array $source, ProductEntity\Payload $payload) : ProductEntity\Product
  {
    $product = new ProductEntity\Product();

    if (isset($result[Properties::PRODUCT_SKU])) {
      $product->properties[Properties::PRODUCT_TYPE] = $source[Properties::PRODUCT_TYPE];
    } else {
      if (empty($payload->product[Properties::PRODUCT_TYPE]))
        throw new Exception(ErrorKey::NEW_PRODUCT_REQUIRES_TYPE);

      $product->isNew = true;
      $product->properties[Properties::PRODUCT_TYPE] = $payload->product[Properties::PRODUCT_TYPE];
    }

    return $product;
  }

  private function fetchDefaultValues(string $productType) : array
  {
    $fileName = '';
    switch ($productType) {
      case 'config':
        $fileName = 'product.json';
        break;
    }

    $filePath = APPLICATION_SRC . 'service/Product/config/default/' . $fileName;
    $values = json_decode(file_get_contents($filePath), true);

    if ($values) return $values;

    throw new Exception(ErrorKey::INVALID_PRODUCT_DEFAULT_VALUES);
  }

  private function chooseDbSchema(string $storage)
  {
    switch($storage) {
      case 'catalog_de': return Core\Database\Connection::SCHEMA_CATALOG_DE;
      case 'catalog_at': return Core\Database\Connection::SCHEMA_CATALOG_AT;
      default:
        return Core\Database\Connection::SCHEMA_CATALOG;
    }
  }

  function saveProduct(ProductEntity\Product $product)
  {
    $this->dbFactory->getProductController()->save_product(
      $product->properties[Properties::PRODUCT_SKU],
      $product->properties[Properties::PRODUCT_STATUS],
      $this->prepareProductPropertiesForDb($product)
    );
  }

  private function prepareProductPropertiesForDb(ProductEntity\Product $product) : string
  {
    $properties = $product->properties;
    unset($properties[Properties::PRODUCT_SKU]);
    unset($properties[Properties::PRODUCT_STATUS]);

    return json_encode($properties);
  }
}
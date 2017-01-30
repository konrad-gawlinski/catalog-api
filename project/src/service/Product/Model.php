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

  function preValidateProduct(array $payload, array $storedProduct)
  {
    if (!isset($storedProduct[Properties::PRODUCT_SKU])
      && empty($payload[Properties::PRODUCT][Properties::PRODUCT_TYPE]))
      throw new Exception(ErrorKey::NEW_PRODUCT_REQUIRES_TYPE);
  }

  function preValidatePayload(array $payload)
  {
    if (empty($payload[Properties::PRODUCT][Properties::PRODUCT_SKU])) {
      return new Exception('Sku is required', 500);
    }

    if (empty($payload[Properties::STORAGE])) {
      return new Exception('Storage is required', 500);
    }
  }

  function createProductEntity(ProductEntity\Payload $payload, array $storedProduct) : ProductEntity\Product
  {
    $product = new ProductEntity\Product();
    $payloadProduct = $payload->product;

    if (isset($storedProduct[Properties::PRODUCT_SKU])) {
      $product->properties[Properties::PRODUCT_TYPE] = $storedProduct[Properties::PRODUCT_TYPE];
    } else {
      $product->isNew = true;
      $product->properties[Properties::PRODUCT_TYPE] = $payloadProduct[Properties::PRODUCT_TYPE];
    }

    $product->properties = array_replace_recursive(
      $this->fetchDefaultValues($product->properties[Properties::PRODUCT_TYPE]),
      $storedProduct,
      $payloadProduct
    );

    return $product;
  }

  function getProductFromStorage(string $sku, string $storage) : array
  {
    $db = $this->dbFactory->getProductController();
    $db->set_schema($this->chooseDbSchema($storage));

    return $db->fetch_product($sku);
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
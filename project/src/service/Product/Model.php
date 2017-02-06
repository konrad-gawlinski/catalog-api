<?php

namespace Nu3\Service\Product;

use Nu3\Core;
use Nu3\Service\Product\Entity as ProductEntity;
use Nu3\Core\Database\Controller\Factory as DbFactory;
use Nu3\Service\Product\Entity\Properties;
use Nu3\Core\Violation;

class Model
{
  /** @var EntityValidator */
  private $validator;

  /** @var  DbFactory */
  private $dbFactory;

  function setEntityValidator(EntityValidator $validator)
  {
    $this->validator = $validator;
  }

  function setDbFactory(DbFactory $factory)
  {
    $this->dbFactory = $factory;
  }

  function validateEntity(ProductEntity\Product $product)
  {
    $this->validator->validate($product);
  }

  function preValidatePayload(array $payload) : array
  {
    $violations = [];

    if (empty($payload[Properties::PRODUCT][Properties::PRODUCT_SKU])) {
      $violations[] = new Violation(ErrorKey::SKU_IS_REQUIRED, Violation::EK_REQUEST);
    }

    if (empty($payload[Properties::STORAGE])) {
      $violations[] = new Violation(ErrorKey::STORAGE_IS_REQUIRED, Violation::EK_REQUEST);
    } else if (!in_array($payload[Properties::STORAGE], ["catalog", "catalog_de", "catalog_at"])) {
        $violations[] = new Violation(ErrorKey::STORAGE_IS_REQUIRED, Violation::EK_REQUEST);
    }

    return $violations;
  }

  function preValidateProduct(array $payload, array $storedProduct) : array
  {
    $violations = [];
    if (!isset($storedProduct[Properties::PRODUCT_SKU])
      && empty($payload[Properties::PRODUCT][Properties::PRODUCT_TYPE]))
        $violations[] = new Violation(ErrorKey::NEW_PRODUCT_REQUIRES_TYPE, Violation::EK_REQUEST);

    return $violations;
  }

  function createProductEntity(ProductEntity\Payload $payload, array $storedProduct) : ProductEntity\Product
  {
    $product = new ProductEntity\Product();
    $payloadProduct = $payload->product;
    $product->sku = $payloadProduct[Properties::PRODUCT_SKU];

    if (isset($storedProduct[Properties::PRODUCT_SKU])) {
      $product->type = $storedProduct[Properties::PRODUCT_TYPE];
    } else {
      $product->isNew = true;
      $product->type = $payloadProduct[Properties::PRODUCT_TYPE];

      $product->properties = array_replace_recursive(
        $this->fetchDefaultValues($product->properties[Properties::PRODUCT_TYPE]),
        $storedProduct
      );
    }

    $product->properties = array_replace_recursive(
      $product->properties,
      $payloadProduct
    );

    return $product;
  }

  function fetchProductType(string $sku, string $storage) : array
  {
    $db = $this->dbFactory->getProductController();
    $db->set_schema($this->chooseDbSchema($storage));

    return $db->fetch_product_type($sku);
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
      $product->sku,
      $product->properties[Properties::PRODUCT_STATUS],
      $this->prepareProductPropertiesForDb($product)
    );
  }

  private function prepareProductPropertiesForDb(ProductEntity\Product $product) : string
  {
    $properties = $product->properties;
    $properties[Properties::PRODUCT_TYPE] = $product->type;
    unset($properties[Properties::PRODUCT_STATUS]);
    unset($properties[Properties::PRODUCT_SKU]);

    return json_encode($properties);
  }
}
<?php

namespace Nu3\Service\Product;

use Nu3\Config;
use Nu3\Core;
use Nu3\Property;
use Nu3\Service\Product\Entity;
use Nu3\Service\Product\Entity\Properties;
use Nu3\Service\Product\Request\ProductSave;

class Model
{
  use Property\Config;

  const DEFAULT_VALUES_DIR = APPLICATION_SRC . 'service/Product/default_values/';

  /** @var EntityValidator */
  private $validator;

  /** @var Core\Database\Controller\Factory */
  private $dbFactory;

  /** @var Entity\DatabaseConverter */
  private $databaseConverter;

  function setEntityValidator(EntityValidator $validator)
  {
    $this->validator = $validator;
  }

  function setDbFactory(Core\Database\Controller\Factory $factory)
  {
    $this->dbFactory = $factory;
  }

  function setDatabaseConverter(Entity\DatabaseConverter $converter)
  {
    $this->databaseConverter = $converter;
  }

  /**
   * @throws Exception
   * @return Core\Violation[]
   */
  function validateEntity(Entity\Product $product) : array
  {
    return $this->validator->validate($product);
  }

  function createProductEntity(ProductSave $productRequest, array $storedProduct) : Entity\Product
  {
    $product = new Entity\Product();
    $payloadProduct = $productRequest->getPayloadProduct();
    $product->sku = $payloadProduct[Properties::PRODUCT_SKU];

    if (isset($storedProduct[Properties::PRODUCT_SKU])) {
      $product->type = $storedProduct[Properties::PRODUCT_TYPE];
    } else {
      $product->isNew = true;
      $product->type = $payloadProduct[Properties::PRODUCT_TYPE];

      $product->properties = array_replace_recursive(
        $this->fetchDefaultValues($product->type),
        $storedProduct
      );
    }

    $product->properties = array_replace_recursive(
      $product->properties,
      $payloadProduct
    );

    return $product;
  }

  function useSchemaByStorage(string $storage)
  {
    $schema = Core\Database\Connection::SCHEMA_CATALOG;
    switch($storage) {
      case 'catalog_de':
        $schema = Core\Database\Connection::SCHEMA_CATALOG_DE; break;
      case 'catalog_at':
        $schema = Core\Database\Connection::SCHEMA_CATALOG_AT; break;
    }

    $db = $this->dbFactory->getProductController();
    $db->set_schema($schema);
  }

  function fetchProductType(string $sku) : array
  {
    $db = $this->dbFactory->getProductController();

    return $db->fetch_product_type($sku);
  }

  private function fetchDefaultValues(string $productType) : array
  {
    $fileName = $this->config()[Config::PRODUCT][$productType][Config::DEFAULT_VALUES];
    $filePath = $this::DEFAULT_VALUES_DIR . $fileName;

    return include($filePath);
  }

  function saveProduct(Entity\Product $product)
  {
    $violations = [];
    try {
      $this->dbFactory->getProductController()->save_product(
        $product->sku,
        $product->properties[Properties::PRODUCT_STATUS],
        $this->databaseConverter->toDatabase($product)
      );
    } catch(Core\Database\Exception $exception) {
      $violations[] = new Core\Violation(ErrorKey::PRODUCT_SAVE_STORAGE_ERROR, Core\Violation::ET_DATABASE);
    }
  }
}

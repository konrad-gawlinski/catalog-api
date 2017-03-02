<?php

namespace Nu3\Service\Product;

use Nu3\Core;
use Nu3\Service\Product\Entity as ProductEntity;
use Nu3\Core\Database\Exception as DbException;
use Nu3\Core\Database\Controller\Factory as DbFactory;
use Nu3\Service\Product\Entity\Properties;
use Nu3\Core\Violation;
use Nu3\Property;
use Nu3\Service\Product\Request\ProductSave;

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

  /**
   * @throws Exception
   * @return Violation[]
   */
  function validateEntity(ProductEntity\Product $product) : array
  {
    return $this->validator->validate($product);
  }

  function createProductEntity(ProductSave $productRequest, array $storedProduct) : ProductEntity\Product
  {
    $product = new ProductEntity\Product();
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
    $fileName = '';
    switch ($productType) {
      case ProductEntity\ProductType::CONFIG:
        $fileName = 'product.json';
        break;
    }

    $filePath = APPLICATION_SRC . 'service/Product/config/default/' . $fileName;
    $values = json_decode(file_get_contents($filePath), true);

    if ($values) return $values;
  }

  function saveProduct(ProductEntity\Product $product, ContentBuilder\Database $builder)
  {
    $violations = [];
    try {
      $this->dbFactory->getProductController()->save_product(
        $product->sku,
        $product->properties[Properties::PRODUCT_STATUS],
        $builder->build($product)
      );
    } catch(DbException $exception) {
      $violations[] = new Violation(ErrorKey::PRODUCT_SAVE_STORAGE_ERROR, Violation::ET_DATABASE);
    }
  }
}
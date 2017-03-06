<?php

namespace Nu3\Service\Product\Request;

use Nu3\Core\Violation;
use Nu3\Service\Product\Entity\Properties as Property;
use Nu3\Service\Product\Entity;
use Nu3\Config;

class ProductSave
{
  use \Nu3\Property\Config;

  const DEFAULT_VALUES_DIR = APPLICATION_SRC . 'service/Product/default_values/';

  private $storedProduct = [];
  private $violations = [];
  private $payload;
  private $validator;

  function __construct(string $json, Validator $validator)
  {
    $this->payload = json_decode($json, true);
    $this->validator = $validator;
  }

  function getPayload() : array
  {
    return $this->payload;
  }

  function getPayloadProduct() : array
  {
    return $this->payload[Property::PRODUCT];
  }

  function getPayloadStorage() : string
  {
    return $this->payload[Property::STORAGE];
  }

  function setStoredProduct(array $product)
  {
    $this->storedProduct = $product;
  }

  function getStoredProduct() : array
  {
    return $this->storedProduct;
  }

  function getViolations() : array
  {
    return $this->violations;
  }

  /**
   * @return Violation[]
   */
  function validatePayload() : array
  {
    $violations = $this->validator->validatePayload($this);
    $this->violations += $violations;

    return $violations;
  }

  /**
   * @return Violation[]
   */
  function validateProduct() : array
  {
    $violations = $this->validator->validateProduct($this);
    $this->violations += $violations;

    return $violations;
  }

  function createProductEntity() : Entity\Product
  {
    $payloadProduct = $this->getPayloadProduct();
    $storedProduct = $this->getStoredProduct();
    $product = new Entity\Product();
    $product->sku = $payloadProduct[Property::PRODUCT_SKU];

    if (isset($storedProduct[Property::PRODUCT_SKU])) {
      $product->type = $storedProduct[Property::PRODUCT_TYPE];
    } else {
      $product->isNew = true;
      $product->type = $payloadProduct[Property::PRODUCT_TYPE];

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

  private function fetchDefaultValues(string $productType) : array
  {
    $fileName = $this->config()[Config::PRODUCT][$productType][Config::DEFAULT_VALUES];
    $filePath = self::DEFAULT_VALUES_DIR . $fileName;

    return include($filePath);
  }
}

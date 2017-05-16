<?php

namespace Nu3\Service\Product;

use Nu3\Config;

class ProductBuilder
{
  const DEFAULT_VALUES_DIR = APPLICATION_SRC . 'service/Product/default_values/';

  /** @var  array */
  private $config;

  function __construct(array $config)
  {
    $this->config = $config;
  }

  function applyPropertiesFromDB(DTO\ProductSave $dto, array $storedProductProperties)
  {
    $productProperties = $dto->getProductProperties();
    if (isset($storedProductProperties[Properties::PRODUCT_SKU])) {
      $productProperties[Properties::PRODUCT_TYPE] = $storedProductProperties[Properties::PRODUCT_TYPE];
      $productProperties[Properties::PRODUCT_STATUS] = $storedProductProperties[Properties::PRODUCT_STATUS];
      $dto->setProductProperties($productProperties);
    } else {
      $dto->setIsNew(true);
    }
  }

  function applyDefaultValues(DTO\ProductSave $dto)
  {
    $productProperties = $dto->getProductProperties();

    if ($dto->getIsNew()) {
      $productProperties = array_replace_recursive(
        $this->fetchDefaultValues($productProperties[Properties::PRODUCT_TYPE]),
        $productProperties
      );

      $dto->setProductProperties($productProperties);
    }
  }

  private function fetchDefaultValues(string $productType) : array
  {
    $fileName = $this->config[Config::PRODUCT][$productType][Config::DEFAULT_VALUES];
    $filePath = self::DEFAULT_VALUES_DIR . $fileName;

    return include($filePath);
  }
}
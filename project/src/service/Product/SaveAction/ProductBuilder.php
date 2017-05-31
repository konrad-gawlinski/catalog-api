<?php

namespace Nu3\Service\Product\SaveAction;

use Nu3\Config;
use Nu3\Service\Product\Property;

class ProductBuilder
{
  const DEFAULT_VALUES_DIR = APPLICATION_SRC . 'service/Product/SaveAction/default_values/';

  /** @var  array */
  private $config;

  function __construct(array $config)
  {
    $this->config = $config;
  }

  function applyPropertiesFromDB(TransferObject $dto, array $storedProductProperties)
  {
    $productProperties = $dto->getProductProperties();
    if (isset($storedProductProperties[Property::PRODUCT_SKU])) {
      $productProperties[Property::PRODUCT_TYPE] = $storedProductProperties[Property::PRODUCT_TYPE];
      $productProperties[Property::PRODUCT_STATUS] = $storedProductProperties[Property::PRODUCT_STATUS];
      $dto->setProductProperties($productProperties);
    } else {
      $dto->setIsNew(true);
    }
  }

  function applyDefaultValues(TransferObject $dto)
  {
    $productProperties = $dto->getProductProperties();

    if ($dto->getIsNew()) {
      $productProperties = array_replace_recursive(
        $this->fetchDefaultValues($productProperties[Property::PRODUCT_TYPE]),
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
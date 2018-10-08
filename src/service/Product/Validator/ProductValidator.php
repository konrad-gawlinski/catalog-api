<?php

namespace Nu3\Service\Product\Validator;

use Nu3\Feature\Config as ConfigFeature;
use Nu3\Feature\RegionUtils;
use Nu3\Service\Product\Factory;
use Nu3\Service\Product\Entity;
use Nu3\Core\Violation;
use Nu3\Config;

class ProductValidator
{
  use ConfigFeature;
  use RegionUtils;

  /** @var Factory */
  protected $factory;

  /** @var array */
  private $validatorsMap = null;

  function __construct(Factory $factory)
  {
    $this->factory = $factory;

    $this->initValidatorsMap();
  }

  private function initValidatorsMap()
  {
    $this->validatorsMap = [
      Config::PRODUCT_TYPE_SIMPLE => $this->factory->createProductSimpleValidator(),
    ];
  }

  /**
   * @return Violation[]
   */
  function validate(Entity\Product $productEntity) : array
  {
    $targetRegionPairs = $this->pickRegionPairs($productEntity);
    $validator = $this->pickValidator($productEntity->type);

    return $validator->validate($productEntity->id, $targetRegionPairs);
  }

  private function pickRegionPairs(Entity\Product $productEntity)
  {
    $allowedRegionPairs = $this->config()[Config::SHOP][Config::REGION_PAIRS];
    if (isset($productEntity->properties[Config::GLOBAL_REGION])) {
      return $allowedRegionPairs;
    }

    return $this->regionUtils->intersectValidRegionPairs(array_keys($productEntity->properties), $allowedRegionPairs);
  }

  private function pickValidator(string $productType) : ValidatableProduct
  {
    return $this->validatorsMap[$productType];
  }
}

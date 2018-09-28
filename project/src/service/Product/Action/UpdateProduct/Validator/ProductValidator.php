<?php

namespace Nu3\Service\Product\Action\UpdateProduct\Validator;

use Nu3\Feature\Config as ConfigFeature;
use Nu3\Feature\RegionUtils;
use Nu3\Service\Product\Action\UpdateProduct\Factory;
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

  /** @var ValidatableProduct[] */
  private $validatorsCache = [];

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
    $allowedRegionPairs = $this->config()[Config::SHOP][Config::REGION_PAIRS];
    $targetRegionPairs = $this->regionUtils->intersectValidRegionPairs(
      array_keys($productEntity->properties), $allowedRegionPairs
    );
    $validator = $this->pickValidator($productEntity->type);

    return $validator->validate($productEntity->id, $targetRegionPairs);
  }

  private function pickValidator(string $productType) : ValidatableProduct
  {
    if (!isset($this->validatorsCache[$productType])) {
      $this->validatorsCache[$productType] = new $this->validatorsMap[$productType];
    }

    return $this->validatorsCache[$productType];
  }
}

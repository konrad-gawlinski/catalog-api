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
    $config = $this->config()[Config::REGION];
    $allowedRegions = array_merge(
      $config[Config::COUNTRY_REGION],
      $config[Config::LANGUAGE_REGION]
    );
    $validator = $this->pickValidator($productEntity->type);

    return $validator->validate($productEntity->id, $allowedRegions);
  }

  private function pickValidator(string $productType) : ValidatableProduct
  {
    return $this->validatorsMap[$productType];
  }
}

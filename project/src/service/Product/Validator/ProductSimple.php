<?php

namespace Nu3\Service\Product\Validator;

use Nu3\Service\Product\Factory;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Service\Product\Entity\Validator as EntityValidator;
use Nu3\Service\Product\EntityBuilder;
use Nu3\Core\Violation;
use Nu3\Core\Database\Exception as DatabaseException;

class ProductSimple implements ValidatableProduct
{
  /** @var ProductGateway */
  protected $productGateway;

  /** @var EntityBuilder */
  private $entityBuilder;

  /** @var EntityValidator */
  private $entityValidator;

  /** @var Factory */
  protected $factory;

  function __construct(Factory $factory)
  {
    $this->factory = $factory;

    $this->entityBuilder = $factory->createEntityBuilder();
    $this->productGateway = $factory->createProductGateway();
    $this->entityValidator = $factory->createEntityValidator();
  }

  /**
   * @param array $regionPairs [['de','de_de'],['com','en_gb']]
   * @return Violation[]
   * @throws DatabaseException
   */
  function validate(int $productId, array $regionPairs) : array
  {
    $productArray = $this->productGateway->fetchProductByIdByRegionPairs($productId, $regionPairs);
    $productEntity = $this->entityBuilder->createEntityFromProductArray($productArray);
    $violations = $this->entityValidator->validate($productEntity);
    if ($violations) return $violations;

    return [];
  }
}

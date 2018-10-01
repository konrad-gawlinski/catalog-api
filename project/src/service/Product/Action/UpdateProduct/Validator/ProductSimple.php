<?php

namespace Nu3\Service\Product\Action\UpdateProduct\Validator;

use Nu3\Service\Product\Action\UpdateProduct\Factory;
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
   * @return Violation[]
   * @throws DatabaseException
   */
  function validate(int $productId, array $regions) : array
  {
    $productArray = $this->productGateway->fetchProductByIdByRegions($productId, $regions);
    $productEntity = $this->entityBuilder->createEntityFromProductArray($productArray);
    $violations = $this->entityValidator->validate($productEntity);
    if ($violations) return $violations;

    return [];
  }
}

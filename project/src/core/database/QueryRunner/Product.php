<?php

namespace Nu3\Core\Database\QueryRunner;

use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Core\Database\Exception as DatabaseException;
use Nu3\Core\Database\Connection;
use Nu3\Core\Database\QueryRunner;

class Product extends ProductGateway
{
  use QueryRunner;

  function __construct(Connection $dbConnection)
  {
    parent::__construct($dbConnection);
  }

  /**
   * @throws DatabaseException
   */
  function productExists(string $sku) : bool
  {
    return $this->runQueryFunction(
      function() use ($sku) {
        return parent::productExists($sku);
      },
      'Product existence could not be verified'
    );
  }

  /**
   * @throws DatabaseException
   */
  function createProduct(?string $sku, string $type, array $properties) : int
  {
    return $this->runQueryFunction(
      function() use ($sku, $type, $properties) {
        return parent::createProduct($sku, $type, $properties);
      },
      'Product could not be created'
    );
  }

  /**
   * @throws \Exception
   */
  function updateProduct(int $id, array $properties) : int
  {
    return $this->runQueryFunction(
      function() use ($id, $properties) {
        return parent::updateProduct($id, $properties);
      },
      'Product could not be updated'
    );
  }

  /**
   * @throws DatabaseException
   */
  public function createNode(int $productId) {
    return $this->runQueryFunction(
      function() use ($productId) {
        parent::createNode($productId);
      },
      'Product node could not be created'
    );
  }

  /**
   * @throws DatabaseException
   */
  public function addToRelationBranch(int $childId, array $branchParentIds) : int
  {
    return $this->runQueryFunction(
      function() use ($childId, $branchParentIds) {
        return parent::addToRelationBranch($childId, $branchParentIds);
      },
      'Relation could not be created'
    );
  }

  /**
   * @throws DatabaseException
   */
  function fetchRawProductById(int $productId,  string $columns = '*') : array
  {
    return $this->runQueryFunction(
      function() use ($productId, $columns) {
        return parent::fetchRawProductById($productId, $columns);
      },
      'Raw product could not be fetched'
    );
  }

  /**
   * @param array $regionPairs e.g. [['de','de_de'],['com','en_gb']]
   * @throws DatabaseException
   */
  function fetchProductById(int $productId, array $regionPairs) : array
  {
    return $this->runQueryFunction(
      function() use ($productId, $regionPairs) {
        return parent::fetchProductById($productId, $regionPairs);
      },
      'Product could not be fetched'
    );
  }
}

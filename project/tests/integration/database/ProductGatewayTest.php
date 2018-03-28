<?php

use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Core\Database\Exception as DatabaseException;

class ProductGatewayTest extends \Codeception\Test\Unit
{
  /**
   * @var \DatabaseTester
   */
  protected $tester;

  /** @var ProductGateway */
  private static $productGateway;

  protected function _before()
  {
    if (!self::$productGateway) {
      self::$productGateway = new ProductGateway($this->tester->dbConnection());
    }
  }

  /**
   * @test
   */
  function it_should_succeed_product_exists_check()
  {
    $pg = $this::$productGateway;

    $this->tester->startTransaction();
    $pg->createProduct('sku_123', 'simple', []);
    $productExists = $pg->productExists('sku_123');
    $this->tester->rollbackTransaction();

    $this->assertTrue($productExists);
  }

  /**
   * @test
   */
  function it_should_fail_product_exists_check()
  {
    $productExists = $this::$productGateway->productExists('foo_999');

    $this->assertFalse($productExists);
  }

  /**
   * @test
   */
  function it_should_create_product()
  {
    $this->tester->startTransaction();
    $productId = $this::$productGateway->createProduct('sku_123', 'simple', []);
    $this->tester->rollbackTransaction();

    $this->assertInternalType('integer', $productId);
  }

  /**
   * @test
   */
  function it_should_fail_product_creation_given_not_existing_column()
  {
    $this->expectException(DatabaseException::class);

    $this->tester->rethrowExceptionInsideTransaction(function () {
      $this::$productGateway->createProduct('sku_123', 'simple', ['foo' => 'bar']);
    });
  }

  /**
   * @test
   */
  function it_should_create_product_node()
  {
    $this->tester->startTransaction();

    $productId = $this::$productGateway->createProduct('sku_123', 'simple', []);
    //it should not throw an exception
    $this::$productGateway->createNode($productId);

    $this->tester->rollbackTransaction();
  }

  /**
   * @test
   */
  function it_should_fail_product_node_creation_given_not_existing_product_id()
  {
    $this->expectException(DatabaseException::class);

    $this->tester->rethrowExceptionInsideTransaction(function () {
      $this::$productGateway->createNode(9999999934);
    });
  }

  /**
   * @test
   * Test following structure simple <- config1 <- config2
   */
  function it_should_create_depth1_product_relation()
  {
    $this->tester->startTransaction();

    list($simpleId, $configId) = $this->createProductsWithNodes([
      ['sku_123', 'simple', []],
      [null, 'config', []]
    ]);
    $this->createRelationsAndAssertCount($configId, [$simpleId]);

    $queryInsertedData = "SELECT * FROM product_relations WHERE parent_id={$simpleId} AND depth > 0";
    $queryExpectedData = "SELECT {$simpleId}, {$configId}, 1";
    $totalMatches = $this->tester->assertQueryResult($queryInsertedData, $queryExpectedData);
    $this->assertEquals(1, $totalMatches);

    $this->tester->rollbackTransaction();
  }

  /**
   * @test
   * Test following structure simple <- config1 <- config2
   */
  function it_should_create_depth2_product_relation()
  {
    $this->tester->startTransaction();

    list($simpleId, $config1Id, $config2Id) = $this->createProductsWithNodes([
      ['sku_123', 'simple', []],
      [null, 'config', []],
      [null, 'config', []]
    ]);
    $this->createRelationsAndAssertCount($config1Id, [$simpleId]);
    $this->createRelationsAndAssertCount($config2Id, [$config1Id, $simpleId]);

    $queryInsertedData = <<<QUERY
SELECT * FROM product_relations WHERE parent_id IN ({$simpleId}, {$config1Id}) AND depth > 0
QUERY;

    $queryExpectedData = <<<QUERY
SELECT {$simpleId}, {$config1Id}, 1
UNION ALL
SELECT {$simpleId}, {$config2Id}, 2
UNION ALL
SELECT {$config1Id}, {$config2Id}, 1
QUERY;

    $totalMatches = $this->tester->assertQueryResult($queryInsertedData, $queryExpectedData);
    $this->assertEquals(3, $totalMatches);

    $this->tester->rollbackTransaction();
  }

  private function createProductsWithNodes(array $products) : array
  {
    $ids = [];
    foreach ($products as list($sku, $type, $properties)) {
      $ids[] = $this::$productGateway->createProduct($sku, $type, $properties);
    }

    return $ids;
  }

  private function createRelationsAndAssertCount(int $childId, array $branchParentIds)
  {
    $totalInsertedRows = $this::$productGateway->addAddToRelationBranch($childId, $branchParentIds);
    $this->assertEquals(count($branchParentIds), $totalInsertedRows);
  }
}

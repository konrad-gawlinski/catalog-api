<?php

use Nu3\Core\Database\QueryRunner\Product as ProductGateway;
use Nu3\Core\Database\Exception as DatabaseException;

class ProductQueryRunnerTest extends \Codeception\Test\Unit
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
  function it_should_update_raw_product()
  {
    $pg = $this::$productGateway;

    $this->tester->startTransaction();
    $productId = $pg->createProduct('sku_123', 'simple', ['global'=> ['name'=>'sample name', 'status'=>'new']]);
    $totalAffectedRows = $pg->updateProduct($productId, ['global'=> ['name'=>'new name']]);
    $product = $pg->fetchRawProductById($productId);
    $this->tester->rollbackTransaction();

    $this->assertEquals(1, $totalAffectedRows);
    $this->assertEquals('{"name": "new name", "status": "new"}', $product['global']);
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

    $queryInsertedData = "SELECT * FROM product_relations WHERE parent_id={$productId}";
    $queryExpectedData = "SELECT {$productId}, {$productId}, 0";
    $totalMatches = $this->tester->assertQueryResult($queryInsertedData, $queryExpectedData);
    $this->assertEquals(1, $totalMatches);

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

  /**
   * @test
   */
  function it_should_fetch_raw_product_by_id()
  {
    $pg = $this::$productGateway;
    $this->tester->startTransaction();
    $productId = $pg->createProduct('sku_123', 'simple', []);
    $product = $pg->fetchRawProductById($productId);
    $this->tester->rollbackTransaction();

    unset($product['created_at']);
    $this->assertEquals([
      'id' => "{$productId}",
      'sku' => 'sku_123',
      'type' => 'simple',
      'global' => '{}',
      'de' => '{}',
      'de_de' => '{}',
      'fr' => '{}',
      'fr_fr' => '{}',
      'at' => '{}',
      'at_de' => '{}'
    ], $product);
  }

  /**
   * @test
   */
  function it_should_fetch_raw_product_by_id_with_specific_columns()
  {
    $pg = $this::$productGateway;
    $this->tester->startTransaction();
    $productId = $pg->createProduct('sku_123', 'simple', []);
    $product = $pg->fetchRawProductById($productId, 'id, sku, type');
    $this->tester->rollbackTransaction();

    unset($product['created_at']);
    $this->assertEquals([
      'id' => "{$productId}",
      'sku' => 'sku_123',
      'type' => 'simple',
    ], $product);
  }

  /**
   * @test
   */
  function it_should_fail_fetching_raw_product_given_non_existing_id()
  {
    $pg = $this::$productGateway;
    $this->tester->startTransaction();
    $product = $pg->fetchRawProductById(999432423);
    $this->tester->rollbackTransaction();

    $this->assertEquals([], $product);
  }

  /**
   * @test
   * Test following structure simple <- config1 <- config2
   */
  function it_should_fetch_product_with_all_inherited_properties()
  {
    $this->tester->startTransaction();

    list($simpleId, $config1Id, $config2Id) = $this->createProductsWithNodes([
      ['sku_123', 'simple', [
        'global' => ['name' => 'sample name', 'icon_bio' => true],
        'de' => ['status' => 'new'],
        'fr' => ['status' => 'new']
      ]],
      [null, 'config', [
        'global' => ['icon_dye' => true],
        'de_de' => ['manufacturer' => 'nu3']
      ]],
      [null, 'config', []]
    ]);
    $this->createRelationsAndAssertCount($config1Id, [$simpleId]);
    $this->createRelationsAndAssertCount($config2Id, [$config1Id, $simpleId]);

    $this->assertEquals(
      [
        'id' => "{$simpleId}",
        'sku' => 'sku_123',
        'type' => 'simple',
        'de-de_de' => '{"name": "sample name", "status": "new", "icon_bio": true, "icon_dye": true, "manufacturer": "nu3"}',
        'fr-fr_fr' => '{"name": "sample name", "status": "new", "icon_bio": true, "icon_dye": true}',
      ],
      $this::$productGateway->fetchProductById($simpleId, [['de','de_de'], ['fr','fr_fr']])
    );

    $this->tester->rollbackTransaction();
  }

  private function createProductsWithNodes(array $products) : array
  {
    $ids = [];
    foreach ($products as list($sku, $type, $properties)) {
      $id = $this::$productGateway->createProduct($sku, $type, $properties);
      $this::$productGateway->createNode($id);
      $ids[] = $id;
    }

    return $ids;
  }

  private function createRelationsAndAssertCount(int $childId, array $branchParentIds)
  {
    $totalInsertedRows = $this::$productGateway->addToRelationBranch($childId, $branchParentIds);
    $this->assertEquals(count($branchParentIds), $totalInsertedRows);
  }
}

<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Service\Product;
use Nu3\Core\Violation;
use Nu3\Core\Database;
use Nu3\Service\Kernel\ViolationsTranslator;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Service\Product\ErrorKey;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action
{
  use ViolationsTranslator;
  
  /** @var Factory */
  private $factory;

  /** @var Product\Action\Validator */
  private $validator;
  
  /** @var ProductGateway */
  private $dbGateway;

  /** @var  Product\Entity\Producer */
  private $entityProducer;

  function __construct(Factory $factory)
  {
    $this->factory = $factory;
    $this->validator = $factory->createValidator();
    $this->entityProducer = $factory->createEntityProducer();
    $this->dbGateway = $factory->createDatabaseGateway();
  }

  function run(Product\Request $request): HttpResponse
  {
    $violations = $this->handleRequest($request);

    if ($violations) {
      return new HttpResponse(
        $this->buildResponseBody($violations),
        $this->returnHttpStatusCode($violations)
      );
    }

    return new HttpResponse('', 204);
  }

  /**
   * @return Violation[]
   */
  private function handleRequest(Product\Request $request) : array
  {
    $violations = $this->validator->validateRequest($request);
    if ($violations) return $violations;

    $dto = $this->factory->createDataTransferObject($request);
    $storedProduct = $this->dbGateway->fetchProduct($dto->getSku());
    if (!$storedProduct) return [new Violation(Product\ErrorKey::PRODUCT_CREATION_RESTRICTED)];

    $productEntity = $this->factory->createProductEntity();
    $productEntity->fillFromDb($storedProduct);
    $this->entityProducer->applyDtoAttributesToEntity($dto, $productEntity);
    $violations = $this->factory->createEntityValidator()->validate($productEntity);

    if (!$violations) {
      $this->factory->createValueFilter()->filterEntity($productEntity);
      return $this->updateProduct($productEntity);
    }

    return [];
  }

  /**
   * @return Violation[]
   */
  private function updateProduct(Product\Entity\Product $product) : array
  {
    try {
      $this->dbGateway->save_product($product->sku, json_encode($product->attributes));
    } catch (Database\Exception $exception) {
      return [new Violation(Product\ErrorKey::PRODUCT_SAVE_STORAGE_ERROR)];
    }

    return [];
  }

  /**
   * @param Violation[] $violations
   *
   * @return string
   */
  private function buildResponseBody(array $violations) : string
  {
    return $this->violationsToJson($violations);
  }

  /**
   * @param Violation[] $violations
   * @return int
   */
  private function returnHttpStatusCode(array $violations) : int
  {
    if (count($violations) > 1) {
      return 400;
    }

    return $this->errorKey2HttpCode($violations[0]->errorKey());
  }

  private function errorKey2HttpCode(string $errorKey) : int
  {
    switch ($errorKey) {
      case ErrorKey::SKU_IS_REQUIRED:
      case ErrorKey::PRODUCT_CREATION_RESTRICTED:
        return 403;

      case ErrorKey::PRODUCT_SAVE_STORAGE_ERROR:
        return 500;

      default:
        return 500;
    }
  }
}

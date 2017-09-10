<?php

namespace Nu3\Service\Product\Action\CreateProduct;

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
  
  /** @var Product\Factory */
  private $factory;

  /** @var Validator */
  private $validator;
  
  /** @var ProductGateway */
  private $dbGateway;

  /** @var  Product\Entity\Producer */
  private $entityProducer;

  function __construct(Product\Action\CreateProduct\Factory $factory)
  {
    http_response_code(500);

    $this->factory = $factory;
    $this->validator = $factory->createValidator();
    $this->entityProducer = $factory->createEntityProducer();
    $this->dbGateway = $factory->createDatabaseGateway();
  }

  function run(Product\Request $request): HttpResponse
  {
    $violations = $this->handleRequest($request);
    $headers = [
      'Content-Type' => 'application/json'
    ];

    if ($violations) {
      return new HttpResponse(
        $this->buildResponseBody($violations),
        $this->returnHttpStatusCode($violations),
        $headers
      );
    }

    return new HttpResponse('', 201, $headers);
  }

  /**
   * @return Violation[]
   */
  private function handleRequest(Product\Request $request) : array
  {
    $violations = $this->validator->validateRequest($request);
    if ($violations) return $violations;

    $dto = $this->factory->createDataTransferObject($request);
    $storedProduct = $this->dbGateway->fetchProductBySku($dto->getSku(), $dto->getCountry(), $dto->getLanguage());
    if ($storedProduct) return [new Violation(ErrorKey::PRODUCT_UPDATE_RESTRICTED)];

    $productEntity = $this->factory->createProductEntity();
    $this->entityProducer->applyDtoAttributesToEntity($dto, $productEntity);
    $violations = $this->factory->createEntityValidator()->validate($productEntity);

    if (!$violations) {
      $this->factory->createValueFilter()->filterEntity($productEntity);
      return $this->createProduct($productEntity, $dto);
    }

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function createProduct(Product\Entity\Product $product, Product\TransferObject $dto) : array
  {
    try {
      $attributeSorter = $this->factory->createAttributeSorter();
      $sortedAttributes = $attributeSorter->sort($dto->getCountry(), $dto->getLanguage(), $product->properties);
      $this->dbGateway->create_product($product->sku, $product->type, json_encode($sortedAttributes));
    } catch (Database\Exception $exception) {
      return [new Violation(ErrorKey::PRODUCT_SAVE_STORAGE_ERROR)];
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
      case ErrorKey::INVALID_LANGUAGE_VALUE:
      case ErrorKey::INVALID_COUNTRY_VALUE:
      case ErrorKey::INVALID_PRODUCT_TYPE:
      case ErrorKey::NEW_PRODUCT_REQUIRES_TYPE:
      case ErrorKey::PRODUCT_UPDATE_RESTRICTED:
      case ErrorKey::PRODUCT_VALIDATION_ERROR:
        return 400;

      default:
        return 500;
    }
  }
}

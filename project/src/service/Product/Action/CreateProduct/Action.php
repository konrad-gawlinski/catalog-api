<?php

namespace Nu3\Service\Product\Action\CreateProduct;

use Nu3\Service\Product\Action\ActionBase;
use Nu3\Service\Product\Validator\ProductValidator;
use Nu3\Service\Product\Entity;
use Nu3\Service\Product\TransferObject;
use Nu3\Core\Violation;
use Nu3\Core\Database;
use Nu3\Service\Product\ErrorKey;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action extends ActionBase
{
  /** @var RequestValidator */
  private $requestValidator;

  /** @var ProductValidator */
  private $productValidator;

  private $violations = [];

  /** @var Builder */
  private $entityBuilder;

  function __construct(Factory $factory)
  {
    parent::__construct($factory);
    
    $this->requestValidator = $factory->createRequestValidator();
    $this->requestValidator->setProductGateway($this->productGateway);
    $this->productValidator = $factory->createProductValidator();
    $this->entityBuilder = $factory->createEntityBuilder();
  }

  function run(Request $request): HttpResponse
  {
    $productId = $this->handleRequest($request);
    $headers = [
      'Content-Type' => 'application/json'
    ];

    if ($this->violations) {
      return new HttpResponse(
        $this->violationsToJson($this->violations),
        $this->returnHttpStatusCode($this->violations),
        $headers
      );
    }

    $headers['Location'] = "/product/{$productId}";
    return new HttpResponse('', 201, $headers);
  }

  private function handleRequest(Request $request) : int
  {
    $this->violations = $this->requestValidator->validate($request);
    if ($this->violations) return 0;

    $dto = $this->factory->createDataTransferObject();
    $dto->applyRequestProperties($request);
    $productEntity = $this->buildProductEntity($dto);
    $productId = $this->saveProduct($productEntity);

    return $productId;
  }

  private function buildProductEntity(TransferObject $dto) : Entity\Product
  {
    $productEntity = $this->factory->createProductEntity();
    $this->entityBuilder->applyDtoAttributesToEntity($dto, $productEntity);
    $this->entityBuilder->applyDefaultAttributesValues($productEntity);
    $this->factory->createValueFilter()->filterEntity($productEntity);

    return $productEntity;
  }

  private function saveProduct(Entity\Product $productEntity) : int
  {
    $this->productGateway->startTransaction();

    try {
      $productId = $this->productGateway->createProduct($productEntity->sku, $productEntity->type, $productEntity->properties);
      $this->productGateway->createNode($productId);
    } catch (Database\Exception $exception) {
      $this->violations = [new Violation(ErrorKey::PRODUCT_SAVE_STORAGE_ERROR)];
      return 0;
    }

    $productEntity->id = $productId;
    $this->violations = $this->productValidator->validate($productEntity);

    if (!$this->violations) {
      $this->productGateway->commitTransaction();
      return $productId;
    }

    $this->productGateway->rollbackTransaction();
    return 0;
  }

  protected function errorKey2HttpCode(string $errorKey) : int
  {
    switch ($errorKey) {
      case ErrorKey::ID_IS_REQUIRED:
      case ErrorKey::SKU_IS_REQUIRED:
      case ErrorKey::ID_HAS_TO_BE_A_NUMBER:
      case ErrorKey::INVALID_LANGUAGE_VALUE:
      case ErrorKey::INVALID_COUNTRY_VALUE:
      case ErrorKey::INVALID_PRODUCT_TYPE:
      case ErrorKey::NEW_PRODUCT_REQUIRES_TYPE:
      case ErrorKey::PRODUCT_ALREADY_CREATED:
      case ErrorKey::PRODUCT_VALIDATION_ERROR:
        return 400;

      case ErrorKey::PRODUCT_SAVE_STORAGE_ERROR:
        return 500;

      default:
        return 500;
    }
  }
}

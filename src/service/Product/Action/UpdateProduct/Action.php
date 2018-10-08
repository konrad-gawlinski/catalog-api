<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Service\Product\Action\ActionBase;
use Nu3\Service\Product\Validator\ProductValidator;
use Nu3\Service\Product\Entity;
use Nu3\Service\Product\EntityBuilder;
use Nu3\Service\Product\ErrorKey;
use Nu3\Service\Product\TransferObject;
use Nu3\Core\Violation;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action extends ActionBase
{
  /** @var RequestValidator */
  private $requestValidator;

  /** @var ProductValidator */
  private $productValidator;

  /** @var EntityBuilder */
  private $entityBuilder;

  function __construct(Factory $factory)
  {
    parent::__construct($factory);

    $this->requestValidator = $factory->createRequestValidator();
    $this->productValidator = $factory->createProductValidator();
    $this->entityBuilder = $factory->createEntityBuilder();
  }

  function run(Request $request): HttpResponse
  {
    $violations = $this->handleRequest($request);

    if ($violations) {
      return new HttpResponse(
        $this->violationsToJson($violations),
        $this->returnHttpStatusCode($violations)
      );
    }

    return new HttpResponse('', 204);
  }

  /**
   * @return Violation[]
   */
  private function handleRequest(Request $request) : array
  {
    $violations = $this->requestValidator->validate($request);
    if ($violations) return $violations;

    $productArray = $this->productGateway->fetchRawProductById(intval($request->getId()));
    if (!$productArray) return [new Violation(ErrorKey::PRODUCT_DOES_NOT_EXIST)];

    $dto = $this->factory->createDataTransferObject();
    $dto->applyRequestProperties($request);
    $product = $this->buildRequestedProductEntity($productArray, $dto);
    $this->factory->createValueFilter()->filterEntity($product);

    return $this->saveProduct($product, $dto);
  }

  private function buildRequestedProductEntity(array $productArray, TransferObject $dto) : Entity\Product
  {
    $productEntity = $this->entityBuilder->createEntityFromProductArray($productArray);
    $productEntity->properties = [];
    $this->entityBuilder->applyDtoAttributesToEntity($dto, $productEntity);
    $this->factory->createValueFilter()->filterEntity($productEntity);

    return $productEntity;
  }

  /**
   * @return Violation[]
   */
  private function saveProduct(Entity\Product $product, TransferObject $dto) : array
  {
    $this->productGateway->startTransaction();

    try {
      $properties = array_intersect_key($product->properties, $dto->properties);
      $this->productGateway->updateProduct($product->id, $properties);
    } catch (\Exception $exception) {
      //TODO: extends the violation object to allow passing error details that can be used for internal logging and are not shown to a user
      return [new Violation(ErrorKey::PRODUCT_SAVE_STORAGE_ERROR)];
    }

    $violations = $this->productValidator->validate($product);
    if (!$violations) {
      $this->productGateway->commitTransaction();
      return [];
    }

    $this->productGateway->rollbackTransaction();
    return $violations;
  }

  protected function errorKey2HttpCode(string $errorKey) : int
  {
    switch ($errorKey) {
      case ErrorKey::ID_IS_REQUIRED:
      case ErrorKey::ID_HAS_TO_BE_A_NUMBER:
      case ErrorKey::INVALID_LANGUAGE_VALUE:
      case ErrorKey::INVALID_COUNTRY_VALUE:
      case ErrorKey::PRODUCT_DOES_NOT_EXIST:
      case ErrorKey::INVALID_PRODUCT_TYPE:
      case ErrorKey::PRODUCT_VALIDATION_ERROR:
      case ErrorKey::EMPTY_PRODUCT_PROPERTIES:
        return 400;

      case ErrorKey::PRODUCT_SAVE_STORAGE_ERROR:
        return 500;

      default:
        return 500;
    }
  }
}

<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Service\Product\Action\ActionBase;
use Nu3\Service\Product\Action\RequestValidator;
use Nu3\Service\Product\Entity;
use Nu3\Service\Product\EntityBuilder;
use Nu3\Service\Product\ErrorKey;
use Nu3\Service\Product\Property;
use Nu3\Service\Product\TransferObject;
use Nu3\Core\Violation;
use Nu3\Core\Database;
use Nu3\Service\Product\ValueFilter;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action extends ActionBase
{
  /** @var RequestValidator */
  private $requestValidator;

  /** @var ValueFilter */
  private $valueFilter;

  /** @var ProductValidator */
  private $productValidator;

  /** @var EntityBuilder */
  private $entityBuilder;

  function __construct(Factory $factory)
  {
    parent::__construct($factory);

    $this->requestValidator = $factory->createRequestValidator();
    $this->valueFilter = $factory->createValueFilter();
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

    $storedProductProperties = $this->productGateway->fetchRawProductById(intval($request->getId()));
    if (!$storedProductProperties) return [new Violation(ErrorKey::PRODUCT_UPDATE_FORBIDDEN)];

    $dto = $this->factory->createDataTransferObject();
    $dto->applyRequestProperties($request);
    $violations = $this->productValidator->validate($storedProductProperties, $dto);
    if ($violations) return $violations;

    $product = $this->buildRequestedProductEntity($storedProductProperties, $dto);

    return $this->saveProduct($product);
  }

  private function buildRequestedProductEntity(array $storedProductProperties, TransferObject $dto) : Entity\Product
  {
    $productEntity = $this->factory->createProductEntity();
    $productEntity->id = $storedProductProperties[Property::PRODUCT_ID];
    $productEntity->sku = $storedProductProperties[Property::PRODUCT_SKU];
    $productEntity->type = $storedProductProperties[Property::PRODUCT_TYPE];
    $this->entityBuilder->applyDtoAttributesToEntity($dto, $productEntity);
    $this->valueFilter->filterEntity($productEntity);

    return $productEntity;
  }

  /**
   * @return Violation[]
   */
  private function saveProduct(Entity\Product $product) : array
  {
    try {
      $this->productGateway->updateProduct($product->id, $product->properties);
    } catch (Database\Exception $exception) {
      return [new Violation(ErrorKey::PRODUCT_SAVE_STORAGE_ERROR)];
    }

    return [];
  }

  protected function errorKey2HttpCode(string $errorKey) : int
  {
    switch ($errorKey) {
      case ErrorKey::ID_IS_REQUIRED:
      case ErrorKey::ID_HAS_TO_BE_A_NUMBER:
      case ErrorKey::INVALID_LANGUAGE_VALUE:
      case ErrorKey::INVALID_COUNTRY_VALUE:
      case ErrorKey::PRODUCT_UPDATE_FORBIDDEN:
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

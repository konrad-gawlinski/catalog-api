<?php

namespace Nu3\Service\Product\Action\GetProduct;

use Nu3\Service\Product\Action\ActionBase;
use Nu3\Core\Violation;
use Nu3\Service\Product\EntityBuilder;
use Nu3\Service\Product\ErrorKey;
use Nu3\Service\Product\Property;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action extends ActionBase
{
  private $violations = [];

  /** @var EntityBuilder */
  private $entityBuilder;

  /** @var RequestValidator */
  private $requestValidator;

  function __construct(Factory $factory)
  {
    parent::__construct($factory);

    $this->requestValidator = $factory->createRequestValidator();
    $this->entityBuilder = $factory->createEntityBuilder();
  }

  function run(Request $request): HttpResponse
  {
    $headers = [
      'Content-Type' => 'application/json'
    ];

    $productProperties = $this->handleRequest($request);

    if ($this->violations) {
      return new HttpResponse(
        $this->violationsToJson($this->violations),
        $this->returnHttpStatusCode($this->violations),
        $headers
      );
    }

    return new HttpResponse($this->buildSuccessResponseBody($productProperties), 200, $headers);
  }

  private function handleRequest(Request $request) : array
  {
    $violations = $this->requestValidator->validate($request);
    if ($violations) {
      $this->violations = $violations;
      return [];
    }

    $productProperties = $this->productGateway->fetchRawProductById(intval($request->getId()));
    if (!$productProperties) {
      $this->violations = [new Violation(ErrorKey::PRODUCT_NOT_FOUND)];
      return [];
    }

    return $productProperties;
  }

  private function buildSuccessResponseBody(array $productProperties) : string
  {
    $productEntity = $this->factory->createProductEntity();
    $this->entityBuilder->fillEntityFromDbArray($productEntity, $productProperties);
    $dto = $this->factory->createDataTransferObject();
    $this->factory->createEntityBuilder()->applyEntityAttributesToDto($productEntity, $dto);

    $productArray = [
      Property::PRODUCT_ID => $productEntity->id,
      Property::PRODUCT_SKU => $productEntity->sku,
      Property::PRODUCT_TYPE => $productEntity->type,
      Property::PRODUCT_PROPERTIES => $productEntity->properties
    ];

    return json_encode($productArray);
  }

  protected function errorKey2HttpCode(string $errorKey) : int
  {
    switch ($errorKey) {
      case ErrorKey::ID_IS_REQUIRED:
      case ErrorKey::ID_HAS_TO_BE_A_NUMBER:
      case ErrorKey::INVALID_LANGUAGE_VALUE:
      case ErrorKey::INVALID_COUNTRY_VALUE:
      case ErrorKey::PRODUCT_VALIDATION_ERROR:
        return 400;

      case ErrorKey::PRODUCT_NOT_FOUND:
        return 404;

      default:
        return 500;
    }
  }
}

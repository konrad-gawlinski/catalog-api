<?php

namespace Nu3\Service\Product\Action\CreateProduct;

use Nu3\Service\Product\Action\ActionBase;
use Nu3\Service\Product\Action\Factory;
use Nu3\Service\Product\Action\CURequest as Request;
use Nu3\Service\Product\Entity;
use Nu3\Service\Product\TransferObject;
use Nu3\Core\Violation;
use Nu3\Core\Database;
use Nu3\Service\Product\ErrorKey;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action extends ActionBase
{
  /** @var Validator */
  private $validator;

  private $violations = [];

  /** @var Builder */
  private $entityBuilder;

  function __construct(Factory $factory)
  {
    parent::__construct($factory);

    $this->factory = $factory;
    $this->validator = $factory->createValidator();
    $this->validator->setProductGateway($this->productGateway);
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
    $this->violations = $this->validator->validateRequest($request);
    if ($this->violations) return 0;

    $dto = $this->factory->createDataTransferObject($request);
    $product = $this->buildProduct($dto);
    $this->violations = $this->factory->createEntityValidator()->validate($product);
    if ($this->violations) return 0;

    $this->factory->createValueFilter()->filterEntity($product);

    return $this->saveProduct($product);
  }

  private function buildProduct(TransferObject $dto) : Entity\Product
  {
    $productEntity = $this->factory->createProductEntity();
    $this->entityBuilder->applyDtoAttributesToEntity($dto, $productEntity);
    $this->entityBuilder->applyDefaultAttributesValues($productEntity);

    return $productEntity;
  }

  private function saveProduct(Entity\Product $product) : int
  {
    try {
      return $this->productGateway->createProduct($product->sku, $product->type, $product->properties);
    } catch (Database\Exception $exception) {
      $this->violations = [new Violation(ErrorKey::PRODUCT_SAVE_STORAGE_ERROR)];
      return 0;
    }
  }

  protected function errorKey2HttpCode(string $errorKey) : int
  {
    switch ($errorKey) {
      case ErrorKey::SKU_IS_REQUIRED:
      case ErrorKey::INVALID_LANGUAGE_VALUE:
      case ErrorKey::INVALID_COUNTRY_VALUE:
      case ErrorKey::INVALID_PRODUCT_TYPE:
      case ErrorKey::NEW_PRODUCT_REQUIRES_TYPE:
      case ErrorKey::PRODUCT_CREATION_FORBIDDEN:
      case ErrorKey::PRODUCT_VALIDATION_ERROR:
        return 400;

      case ErrorKey::PRODUCT_SAVE_STORAGE_ERROR:
        return 500;

      default:
        return 500;
    }
  }
}

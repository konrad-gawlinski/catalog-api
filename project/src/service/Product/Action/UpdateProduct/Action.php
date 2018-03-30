<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Service\Product\Action\ActionBase;
use Nu3\Service\Product\Action\Validator;
use Nu3\Service\Product\Entity;
use Nu3\Service\Product\ErrorKey;
use Nu3\Service\Product\Property;
use Nu3\Service\Product\TransferObject;
use Nu3\Core\Violation;
use Nu3\Core\Database;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action extends ActionBase
{
  /** @var Validator */
  private $validator;

  /** @var Entity\Builder */
  private $entityBuilder;

  function __construct(Factory $factory)
  {
    parent::__construct($factory);

    $this->factory = $factory;
    $this->validator = $factory->createValidator();
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
    $violations = $this->validator->validateRequest($request);
    if ($violations) return $violations;

    $storedProduct = $this->productGateway->fetchProductById(intval($request->getId()));
    if (!$storedProduct) return [new Violation(ErrorKey::PRODUCT_UPDATE_FORBIDDEN)];

    $dto = $this->factory->createDataTransferObject($request);
    $product = $this->buildStoredProduct($storedProduct, $dto);
    $violations = $this->factory->createEntityValidator()->validate($product);
    if ($violations) return $violations;

    $product = $this->buildRequestedProduct($storedProduct, $dto);

//    return $this->saveProduct($product, $dto);
  }

  private function buildStoredProduct(array $storedProduct, TransferObject $dto) : Entity\Product
  {
    $productEntity = $this->factory->createProductEntity();
    $productEntity->fillFromDb($storedProduct);
    $this->entityBuilder->applyDtoAttributesToEntity($dto, $productEntity);

    return $productEntity;
  }

  private function buildRequestedProduct(array $storedProduct, TransferObject $dto) : Entity\Product
  {
    $productEntity = $this->factory->createProductEntity();
    $productEntity->id = $storedProduct[Property::PRODUCT_ID];
    $productEntity->sku = $storedProduct[Property::PRODUCT_SKU];
    $productEntity->type = $storedProduct[Property::PRODUCT_TYPE];
    $this->entityBuilder->applyDtoAttributesToEntity($dto, $productEntity);
    $this->factory->createValueFilter()->filterEntity($productEntity);

    return $productEntity;
  }

  /**
   * @return Violation[]
   */
  private function saveProduct(Entity\Product $product, TransferObject $dto) : array
  {
    try {
      $this->productGateway->update_product($product->sku, $product->properties);
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
        return 400;

      case ErrorKey::PRODUCT_SAVE_STORAGE_ERROR:
        return 500;

      default:
        return 500;
    }
  }
}

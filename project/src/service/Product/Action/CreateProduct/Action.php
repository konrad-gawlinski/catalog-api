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

  /** @var Builder */
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
    $headers = [
      'Content-Type' => 'application/json'
    ];

    if ($violations) {
      return new HttpResponse(
        $this->violationsToJson($violations),
        $this->returnHttpStatusCode($violations),
        $headers
      );
    }

    return new HttpResponse('', 201, $headers);
  }

  /**
   * @return Violation[]
   */
  private function handleRequest(Request $request) : array
  {
    $violations = $this->validator->validateRequest($request);
    if ($violations) return $violations;

    $dto = $this->factory->createDataTransferObject($request);
    $storedProduct = $this->dbGateway->fetchProductBySku($dto->getSku(), $dto->getCountry(), $dto->getLanguage());
    if ($storedProduct) return [new Violation(ErrorKey::PRODUCT_UPDATE_RESTRICTED)];

    $product = $this->buildProduct($dto);
    $violations = $this->factory->createEntityValidator()->validate($product);
    if ($violations) return $violations;

    $this->factory->createValueFilter()->filterEntity($product);

    return $this->saveProduct($product, $dto);
  }

  /**
   * @param $dto
   * @return Entity\Product
   */
  private function buildProduct($dto) : Entity\Product
  {
    $productEntity = $this->factory->createProductEntity();
    $this->entityBuilder->applyDtoAttributesToEntity($dto, $productEntity);
    $this->entityBuilder->applyDefaultAttributesValues($productEntity);

    return $productEntity;
  }

  /**
   * @return Violation[]
   */
  private function saveProduct(Entity\Product $product, TransferObject $dto) : array
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

  protected function errorKey2HttpCode(string $errorKey) : int
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

      case ErrorKey::PRODUCT_SAVE_STORAGE_ERROR:
        return 500;

      default:
        return 500;
    }
  }
}

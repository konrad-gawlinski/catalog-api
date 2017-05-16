<?php

namespace Nu3\Service\Product;

use Nu3\Core\Violation;
use Nu3\Core\Database;
use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\Request\ProductSave as ProductSaveRequest;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Symfony\Component\HttpFoundation\Response;

class SaveAction
{
  /** @var  ProductSaveFactory */
  private $factory;

  function __construct(ProductSaveFactory $factory)
  {
    $this->factory = $factory;
  }

  function run(ProductSaveRequest $productRequest, ProductGateway $productGateway): Response
  {
    $violations = $this->handleRequest($productRequest, $productGateway);

    if ($violations) {
      return new Response($this->violationsToJson($violations), 513);
    }

    return new Response('', 200);
  }

  /**
   * @return Violation[]
   */
  private function handleRequest(ProductSaveRequest $productRequest, ProductGateway $productGateway) : array
  {
    $validator = $this->factory->createValidator();
    $violations = $validator->validateRequest($productRequest);
    if ($violations) return $violations;

    $dto = $this->factory->createDataTransferObject($productRequest);
    $productGateway->setSchemaByStorage($dto->getStorage());
    $storedProduct = $productGateway->fetchProductBasicSet(
      $dto->getProductProperties()[Properties::PRODUCT_SKU]
    );

    $violations = $validator->validateProduct($dto, $storedProduct);
    if ($violations) return $violations;

    $this->hydrateDto($dto, $storedProduct);
    $productEntity = $this->factory->createProductEntityFromDto($dto);
    $violations = $this->factory->createEntityValidator()->validate($productEntity);

    if (!$violations) {
      $this->factory->createPropertyValueFilter()->filterEntity($productEntity);
      return $this->saveProduct($productEntity, $productGateway);
    }

    return [];
  }

  private function hydrateDto(DTO\ProductSave $dto, array $storedProductProperties)
  {
    $productBuilder = $this->factory->createProductBuilder();
    $productBuilder->applyPropertiesFromDB($dto, $storedProductProperties);
    $productBuilder->applyDefaultValues($dto);
  }

  /**
   * @return Violation[]
   */
  private function saveProduct(Product $product, ProductGateway $productGateway) : array
  {
    try {
      $productGateway->save_product(
        $product->sku,
        $product->status,
        json_encode($product->properties)
      );
    } catch (Database\Exception $exception) {
      return [new Violation(ErrorKey::PRODUCT_SAVE_STORAGE_ERROR, Violation::ET_DATABASE)];
    }

    return [];
  }

  /**
   * @param Violation[] $violations
   *
   * @return string
   */
  private function violationsToJson(array $violations) : string
  {
    $result = [];
    /** @var Violation $violation */
    foreach ($violations as $violation) {
      $result[] = $violation->message();
    }

    return json_encode(($result));
  }
}

<?php

namespace Nu3\Service\Product\SaveAction;

use Nu3\Core\Violation;
use Nu3\Core\Database;
use Nu3\Service\Kernel\ViolationsTranslator;
use Nu3\Service\Product\Entity\Product;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Service\Product\Property;
use Nu3\Service\Product\ErrorKey;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action
{
  use ViolationsTranslator;
  
  /** @var  Factory */
  private $factory;

  function __construct(Factory $factory)
  {
    $this->factory = $factory;
  }

  function run(Request $request, ProductGateway $productGateway): HttpResponse
  {
    $violations = $this->handleRequest($request, $productGateway);

    if ($violations) {
      return new HttpResponse($this->violationsToJson($violations), 513);
    }

    return new HttpResponse('', 200);
  }

  /**
   * @return Violation[]
   */
  private function handleRequest(Request $request, ProductGateway $productGateway) : array
  {
    $validator = $this->factory->createValidator();
    $violations = $validator->validateRequest($request);
    if ($violations) return $violations;

    $dto = $this->factory->createDataTransferObject($request);
    $productGateway->setSchemaByStorage($dto->getStorage());
    $storedProduct = $productGateway->fetchProductBasicSet(
      $dto->getProductProperties()[Property::PRODUCT_SKU]
    );

    $violations = $validator->validateProduct($dto, $storedProduct);
    if ($violations) return $violations;

    $this->hydrateDto($dto, $storedProduct);
    $productEntity = $this->factory->createProductEntityFromDto($dto);
    $violations = $this->factory->createEntityValidator()->validate($productEntity);

    if (!$violations) {
      $this->factory->createValueFilter()->filterEntity($productEntity);
      return $this->saveProduct($productEntity, $productGateway);
    }

    return [];
  }

  private function hydrateDto(TransferObject $dto, array $storedProductProperties)
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
}

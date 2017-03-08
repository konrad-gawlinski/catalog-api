<?php

namespace Nu3\Service\Product;

use Nu3\Core\Violation;
use Nu3\Core\Database;
use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\Request\ProductSave as ProductSaveRequest;
use Nu3\Service\Product\Entity\Properties as ProductProperty;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Symfony\Component\HttpFoundation\Response;

class SaveAction
{
  function run(ProductSaveRequest $productRequest, ProductGateway $productGateway, EntityValidator $validator): Response
  {
    $this->init($productRequest, $productGateway);
    $violations = $productRequest->getViolations();
    if (!$violations) {
      $productEntity = $productRequest->createProductEntity();
      $violations = $validator->validate($productEntity);

      if (!$violations) {
        $violations = $this->saveProduct($productEntity, $productGateway);
      }
    }

    if ($violations) {
      return new Response($this->violationsToJson($violations), 513);
    }

    return new Response('', 200);
  }

  private function init(ProductSaveRequest $productRequest, ProductGateway $productGateway)
  {
    $violations = $productRequest->validatePayload();
    if ($violations) return;

    $productGateway->setSchemaByStorage($productRequest->getPayloadStorage());
    $storedProduct = $productGateway->fetchProductType(
      $productRequest->getPayloadProduct()[ProductProperty::PRODUCT_SKU]
    );
    $productRequest->setStoredProduct($storedProduct);

    $productRequest->validateProduct();
  }

  /**
   * @return Violation[]
   */
  private function saveProduct(Product $product, ProductGateway $productGateway) : array
  {
    try {
      $productGateway->save_product(
        $product->sku,
        $product->properties[ProductProperty::PRODUCT_STATUS],
        $this->prepareProduct($product)
      );
    } catch (Database\Exception $exception) {
      return [new Violation(ErrorKey::PRODUCT_SAVE_STORAGE_ERROR, Violation::ET_DATABASE)];
    }

    return [];
  }

  private function prepareProduct(Entity\Product $product) : string
  {
    $properties = $product->properties;
    $properties[ProductProperty::PRODUCT_TYPE] = $product->type;
    unset($properties[ProductProperty::PRODUCT_STATUS]);
    unset($properties[ProductProperty::PRODUCT_SKU]);

    return json_encode($properties);
  }

  /**
   * @param Violation[] $violations
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

<?php

namespace Nu3\Service\Product;

use Nu3\Core\Violation;
use Nu3\Service\Product\Request\ProductSave as ProductSaveRequest;
use Nu3\Service\Product\Entity\Properties as ProductProperty;
use Symfony\Component\HttpFoundation\Response;

class SaveAction
{
  function run(ProductSaveRequest $productRequest, Model $productModel, ContentBuilder\Database $builder): Response
  {
    $this->init($productModel, $productRequest);
    $violations = $productRequest->getViolations();
    if (!$violations) {
      $violations = $this->saveProduct($productRequest, $productModel, $builder);
    }

    var_dump('Violations: ', $violations);
    return new Response('Product save action', 200);
  }

  private function init(Model $productModel, ProductSaveRequest $productRequest)
  {
    $violations = $productRequest->validatePayload();
    if ($violations) return;

    $productModel->useSchemaByStorage($productRequest->getPayloadStorage());
    $storedProduct = $productModel->fetchProductType(
      $productRequest->getPayloadProduct()[ProductProperty::PRODUCT_SKU]
    );
    $productRequest->setStoredProduct($storedProduct);

    $productRequest->validateProduct();
  }

  /**
   * @return Violation[]
   */
  private function saveProduct(ProductSaveRequest $productRequest, Model $productModel, ContentBuilder\Database $builder) : array
  {
    try {
      $productEntity = $productModel->createProductEntity($productRequest, $productRequest->getStoredProduct());
      $violations = $productModel->validateEntity($productEntity);
      if ($violations) return $violations;

      $productModel->saveProduct($productEntity, $builder);
    } catch(Exception $serviceException) {
      return [$serviceException->getViolation()];
    }

    return [];
  }
}

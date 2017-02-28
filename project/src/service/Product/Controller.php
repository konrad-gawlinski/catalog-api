<?php

namespace Nu3\Service\Product;

use Nu3\Core\Violation;
use Nu3\Service\Product\Request\ProductSave as ProductSaveRequest;
use Nu3\Service\Product\Entity\Properties as ProductProperty;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
  function save(ProductSaveRequest $productRequest, Model $productModel): Response
  {
    $this->init($productModel, $productRequest);

    if ($productRequest->isValid()) {
      $violations = $this->saveProduct($productRequest, $productModel);
    } else {
      $violations = $productRequest->getViolations();
    }

    var_dump('Violations: ', $violations);
    return new Response('Product save action', 200);
  }

  private function init(Model $productModel, ProductSaveRequest $productRequest)
  {
    $violations = $productRequest->preValidatePayload();
    if ($violations) return;

    $storedProduct = $productModel->fetchProductType(
      $productRequest->getPayloadProduct()[ProductProperty::PRODUCT_SKU],
      $productRequest->getPayloadStorage()
    );
    $productRequest->setStoredProduct($storedProduct);

    $productRequest->preValidateProduct($storedProduct);
  }

  /**
   * @return Violation[]
   */
  private function saveProduct(ProductSaveRequest $productRequest, Model $productModel) : array
  {
    try {
      $productEntity = $productModel->createProductEntity($productRequest, $productRequest->getStoredProduct());
      $violations = $productModel->validateEntity($productEntity);
      if ($violations) return $violations;

      $productModel->saveProduct($productEntity);
    } catch(Exception $serviceException) {
      return [$serviceException->getViolation()];
    }

    return [];
  }
}

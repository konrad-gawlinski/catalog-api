<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Request\ProductSave as ProductSaveRequest;
use Symfony\Component\HttpFoundation\Response;
use Nu3\Service\Product\Entity\Properties as ProductProperty;

class Controller
{
  function save(ProductSaveRequest $productRequest, Model $productModel): Response
  {
    $violations = [];
    $this->init($productModel, $productRequest);

    if ($productRequest->isValid()) {
      $productEntity = $productModel->createProductEntity($productRequest, $productRequest->getStoredProduct());
      $productModel->validateEntity($productEntity);

      $productModel->saveProduct($productEntity);
    } else {
      $violations = $productRequest->getViolations();
    }

    var_dump('Violations: ', $violations);
    return new Response('Product save action', 200);
  }

  private function init(Model $productModel, ProductSaveRequest $productRequest)
  {
    $violations = $productRequest->preValidatePayload();
    if (isset($violations[0])) return;

    $storedProduct = $productModel->fetchProductType(
      $productRequest->getPayloadProduct()[ProductProperty::PRODUCT_SKU],
      $productRequest->getPayloadStorage()
    );
    $productRequest->setStoredProduct($storedProduct);

    $productRequest->preValidateProduct($storedProduct);
  }
}

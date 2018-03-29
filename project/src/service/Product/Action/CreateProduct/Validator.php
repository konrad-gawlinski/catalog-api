<?php

namespace Nu3\Service\Product\Action\CreateProduct;

use Nu3\Core\Violation;
use Nu3\Feature\Config;
use Nu3\Service\Product\Action\CURequest as Request;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Service\Product\Property;
use Nu3\Service\Product\ErrorKey;
use Nu3\Service\Product\Action\ValidationTrait;

class Validator implements \Nu3\Service\Product\Action\Validator
{
  use Config;
  use ValidationTrait\AllowedProductType;

  /** @var ProductGateway */
  private $productGateway;

  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validateRequest($request) : array
  {
    $violations = $this->validateRequiredSku($request->getSku());
    $violations = array_merge($violations, $this->validateProductType($request->getPayload()));
    $violations = array_merge($violations, $this->makeSureProductDoesNotExist($request->getSku()));

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateRequiredSku(string $sku) : array
  {
    if (empty($sku)) {
      return [new Violation(ErrorKey::SKU_IS_REQUIRED)];
    }

    return [];
  }

  /**
   * @return Violation[]
   */
  private function validateProductType(array $payload) : array
  {
      $violations = $this->validateRequiredProductType($payload);
      if (!$violations)
        $violations = $this->validateAllowedProductType($payload);

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateRequiredProductType(array $payload) : array
  {
    if (empty($payload[Property::PRODUCT_TYPE]))
      return [new Violation(ErrorKey::NEW_PRODUCT_REQUIRES_TYPE)];

    return [];
  }

  private function makeSureProductDoesNotExist($sku) : array
  {
    if ($this->productGateway->productExists($sku))
      return [new Violation(ErrorKey::PRODUCT_CREATION_FORBIDDEN)];

    return [];
  }

  function setProductGateway(ProductGateway $productGateway)
  {
    $this->productGateway = $productGateway;
  }
}

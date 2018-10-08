<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Core\Violation;
use Nu3\Service\Product\ErrorKey;
use Nu3\Service\Product\Feature\RequiredIdValidator;
use Nu3\Service\Product\Property;

class RequestValidator implements \Nu3\Service\Product\Action\RequestValidator
{
  use RequiredIdValidator;

  /** @var ProductGateway */
  protected $productGateway;

  function setProductGateway(ProductGateway $productGateway)
  {
    $this->productGateway = $productGateway;
  }

  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validate($request) : array
  {
    $violations = $this->validateRequiredId($request->getId());
    $violations = array_merge($violations, $this->rejectEmptyBody($request->getPayload()));

    return $violations;
  }

  private function rejectEmptyBody(array $payload)
  {
    if (!isset($payload[Property::PRODUCT_PROPERTIES])) {
      return [new Violation(ErrorKey::EMPTY_PRODUCT_PROPERTIES)];
    }

    return [];
  }
}

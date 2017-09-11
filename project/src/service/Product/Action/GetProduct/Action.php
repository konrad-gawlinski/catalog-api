<?php

namespace Nu3\Service\Product\Action\GetProduct;

use Nu3\Service\Product\Action\ActionBase;
use Nu3\Service\Product\Action\Factory;
use Nu3\Service\Product\Request;
use Nu3\Core\Violation;
use Nu3\Service\Product\ErrorKey;
use Nu3\Service\Product\Property;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action extends ActionBase
{
  function __construct(Factory $factory)
  {
    parent::__construct($factory);

    $this->factory = $factory;
  }

  function run(Request $request): HttpResponse
  {
    $violations = $this->factory->createValidator()->validateRequest($request);
    if ($violations) {
      return $this->buildResponse($this->violationsToJson($violations), 403);
    }

    $productArray = $this->dbGateway->fetchProductBySku($request->getSku(), $request->getCountry(), $request->getLanguage());
    if (!$productArray) {
      return $this->buildResponse($this->violationsToJson([new Violation(ErrorKey::PRODUCT_NOT_FOUND)]), 403);
    }

    return $this->buildResponse($this->buildSuccessResponseBody($productArray), 200);
  }

  private function buildResponse($body, $httpCode) : HttpResponse
  {
    $headers = [
      'Content-Type' => 'application/json'
    ];

    return new HttpResponse($body, $httpCode, $headers);
  }

  private function buildSuccessResponseBody(array $product) : string
  {
    $productEntity = $this->factory->createProductEntity();
    $productEntity->fillFromDb($product);

    $productArray = [
        Property::PRODUCT_ID => $productEntity->id,
        Property::PRODUCT_SKU => $productEntity->sku,
        Property::PRODUCT_TYPE => $productEntity->type,
      ] + $productEntity->properties;

    return json_encode($productArray);
  }
}

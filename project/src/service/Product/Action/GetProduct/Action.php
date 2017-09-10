<?php

namespace Nu3\Service\Product\Action\GetProduct;

use Nu3\Core\Database;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Core\Violation;
use Nu3\Service\Kernel\ViolationsTranslator;
use Nu3\Service\Product\ErrorKey;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action
{
  use ViolationsTranslator;
  
  /** @var Factory */
  private $factory;

  /** @var ProductGateway */
  private $dbGateway;

  function __construct(Factory $factory)
  {
    $this->factory = $factory;
    $this->dbGateway = $factory->createDatabaseGateway();
  }

  function run(Request $request): HttpResponse
  {
    $violations = $this->factory->createValidator()->validateRequest($request);
    if ($violations) {
      return new HttpResponse($this->violationsToJson($violations), 513);
    }

    $productArray = $this->dbGateway->fetchProductBySku($request->sku(), $request->country(), $request->language());
    if (!$productArray) {
      return new HttpResponse($this->violationsToJson([new Violation(ErrorKey::PRODUCT_NOT_FOUND)]), 513);
    }

    $response = $this->buildResponse($productArray);

    return new HttpResponse(json_encode($response->getProperties()), 200, [
      'Content-Type' => 'application/json'
    ]);
  }

  private function buildResponse(array $product) : Response
  {
    $productEntity = $this->factory->createProductEntityFromDB($product);

    return $this->factory->createProductResponse($productEntity);
  }
}

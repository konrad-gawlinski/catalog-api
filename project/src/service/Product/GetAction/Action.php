<?php

namespace Nu3\Service\Product\GetAction;

use Nu3\Core\Database;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Service\Kernel\ViolationsTranslator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Action
{
  use ViolationsTranslator;
  
  /** @var Factory */
  private $factory;

  function __construct(Factory $factory)
  {
    $this->factory = $factory;
  }

  function run(Request $request, ProductGateway $productGateway): HttpResponse
  {
    $violations = $this->factory->createValidator()->validateRequest($request);
    if ($violations) {
      return new HttpResponse($this->violationsToJson($violations), 513);
    }

    $response = $this->buildResponse($request, $productGateway);

    return new HttpResponse(json_encode($response->getProperties()), 200, [
      'Content-Type' => 'application/json'
    ]);
  }

  private function buildResponse(Request $request, ProductGateway $productGateway) : Response
  {
    $productArray = $productGateway->fetchProduct($request->sku());
    $productEntity = $this->factory->createProductEntityFromDB($productArray);

    return $this->factory->createProductResponse($productEntity);
  }
}

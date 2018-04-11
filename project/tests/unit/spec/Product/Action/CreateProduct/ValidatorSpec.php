<?php

namespace spec\Product\Nu3\Service\Product\Action\CreateProduct;

use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Service\Product\Action\CreateProduct\Request;
use Nu3\Service\Product\Property;
use Nu3\Spec\App;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidatorSpec extends ObjectBehavior
{
  function it_should_succeed(ProductGateway $productGateway, Request $request)
  {
    $this->mockDependencies($productGateway, false);
    $this->setupRequestMock($request, $this->getValidPayload());

    $this->validateRequest($request)->shouldHaveCount(0);
  }

  function it_should_fail_given_no_sku(ProductGateway $productGateway, Request $request)
  {
    $this->mockDependencies($productGateway, false);
    $payload = $this->getValidPayload();
    unset($payload[Property::PRODUCT_SKU]);
    $this->setupRequestMock($request, $payload);

    $this->validateRequest($request)->shouldHaveCount(1);
  }

  function it_should_fail_given_no_type(ProductGateway $productGateway, Request $request)
  {
    $this->mockDependencies($productGateway, false);
    $payload = $this->getValidPayload();
    unset($payload[Property::PRODUCT_TYPE]);
    $this->setupRequestMock($request, $payload);

    $this->validateRequest($request)->shouldHaveCount(1);
  }

  function it_should_fail_given_wrong_type(ProductGateway $productGateway, Request $request)
  {
    $this->mockDependencies($productGateway, false);
    $payload = $this->getValidPayload();
    $payload[Property::PRODUCT_TYPE] = 'wrong_type';
    $this->setupRequestMock($request, $payload);

    $this->validateRequest($request)->shouldHaveCount(1);
  }

  function it_should_fail_given_product_does_exist(ProductGateway $productGateway, Request $request)
  {
    $this->mockDependencies($productGateway, true);
    $this->setupRequestMock($request, $this->getValidPayload());

    $this->validateRequest($request)->shouldHaveCount(1);
  }

  function it_should_fail_causing_2_violations(ProductGateway $productGateway, Request $request)
  {
    $this->mockDependencies($productGateway, true);
    $payload = $this->getValidPayload();
    $payload[Property::PRODUCT_TYPE] = 'wrong_type';
    $this->setupRequestMock($request, $payload);

    $this->validateRequest($request)->shouldHaveCount(2);
  }

  private function mockDependencies(ProductGateway $productGateway, bool $productExists)
  {
    $productGateway->productExists(Argument::any())->willReturn($productExists);
    $this->setProductGateway($productGateway);
    $this->setConfig(App::getInstance()->getConfig());
  }

  private function setupRequestMock(Request $request, array $payload)
  {
    $request->getPayload()->willReturn($payload);
  }

  private function getValidPayload() : array
  {
    return [
      Property::PRODUCT_SKU => 'sku_123',
      Property::PRODUCT_TYPE => 'config'
    ];
  }
  
}

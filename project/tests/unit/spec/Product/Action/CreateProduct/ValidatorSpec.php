<?php

namespace spec\Product\Nu3\Service\Product\Action\CreateProduct;

use Nu3\Service\Product\Action\CreateProduct\Validator;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Service\Product\Action\CURequest;
use Nu3\Service\Product\Property;
use Nu3\Spec\App;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidatorSpec extends ObjectBehavior
{
  function it_is_initializable()
  {
    $this->shouldHaveType(Validator::class);
  }

  function it_should_succeed(ProductGateway $productGateway, CURequest $request)
  {
    $this->mockDependencies($productGateway, false);
    $this->setupRequestMock($request, 'sku_123', $this->getValidPayload());

    $this->validateRequest($request)->shouldHaveCount(0);
  }

  function it_should_fail_given_no_sku(ProductGateway $productGateway, CURequest $request)
  {
    $this->mockDependencies($productGateway, false);
    $this->setupRequestMock($request, '', $this->getValidPayload());

    $this->validateRequest($request)->shouldHaveCount(1);
  }

  function it_should_fail_given_no_type(ProductGateway $productGateway, CURequest $request)
  {
    $this->mockDependencies($productGateway, false);
    $payload = $this->getValidPayload();
    unset($payload[Property::PRODUCT_TYPE]);
    $this->setupRequestMock($request, 'sku_123', $payload);

    $this->validateRequest($request)->shouldHaveCount(1);
  }

  function it_should_fail_given_wrong_type(ProductGateway $productGateway, CURequest $request)
  {
    $this->mockDependencies($productGateway, false);
    $payload = $this->getValidPayload();
    $payload[Property::PRODUCT_TYPE] = 'wrong_type';
    $this->setupRequestMock($request, 'sku_123', $payload);

    $this->validateRequest($request)->shouldHaveCount(1);
  }

  function it_should_fail_given_product_does_exist(ProductGateway $productGateway, CURequest $request)
  {
    $this->mockDependencies($productGateway, true);
    $this->setupRequestMock($request, 'sku_123', $this->getValidPayload());

    $this->validateRequest($request)->shouldHaveCount(1);
  }

  function it_should_fail_causing_2_violations(ProductGateway $productGateway, CURequest $request)
  {
    $this->mockDependencies($productGateway, true);
    $payload = $this->getValidPayload();
    $payload[Property::PRODUCT_TYPE] = 'wrong_type';
    $this->setupRequestMock($request, 'sku_123', $payload);

    $this->validateRequest($request)->shouldHaveCount(2);
  }

  private function mockDependencies(ProductGateway $productGateway, bool $productExists)
  {
    $productGateway->productExists(Argument::any())->willReturn($productExists);
    $this->setProductGateway($productGateway);
    $this->setConfig(App::getInstance()->getConfig());
  }

  private function setupRequestMock(CURequest $request, string $sku, array $payload)
  {
    $request->getSku()->willReturn($sku);
    $request->getPayload()->willReturn($payload);
  }

  private function getValidPayload() : array
  {
    return [
      Property::PRODUCT_TYPE => 'config'
    ];
  }
  
}

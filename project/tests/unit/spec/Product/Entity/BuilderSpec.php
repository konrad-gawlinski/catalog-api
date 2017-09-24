<?php

namespace spec\Product\Nu3\Service\Product\Entity;

use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\TransferObject;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BuilderSpec extends ObjectBehavior
{
  function it_should_apply_dto_attributes_to_entity(TransferObject $dto, Product $entity)
  {
    $_dto = $this->mockDto($dto, 'nu3_36', [
      Product::TYPE => 'Config',
      'status' => 'approved',
      'name' => 'some name'
    ]);

    $this->verifyExpectedAttributes($_dto, $entity, 'nu3_36', [
      'status' => 'approved',
      'name' => 'some name'
    ]);
  }

  function it_should_ignore_unknown_property(TransferObject $dto, Product $entity)
  {
    $_dto = $this->mockDto($dto, 'nu3_36', [
      Product::TYPE => 'Config',
      'status' => 'approved',
      'name' => 'some name',
      'unknown' => 'value'
    ]);

    $this->verifyExpectedAttributes($_dto, $entity, 'nu3_36', [
      'status' => 'approved',
      'name' => 'some name'
    ]);
  }

  private function mockDto(TransferObject $dto, string $sku, array $properties)
  {
    $dto->getSku()->willReturn($sku);
    $dto->getProductProperties()->willReturn($properties);

    return $dto;
  }

  private function verifyExpectedAttributes(
    TransferObject $dto, Product $entity,
    string $sku, array $expectedProperties
  ) {
    $_entity = $this->applyDtoAttributesToEntity($dto, $entity);
    $_entity->__get('sku')->shouldReturn($sku);
    $_entity->__get('properties')->shouldReturn($expectedProperties);
  }
}

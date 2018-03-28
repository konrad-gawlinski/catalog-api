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
    $dtoMock = $this->mockDto($dto, 'nu3_36', 'config', [
      'global' => [
        'status' => 'approved',
        'name' => 'some name'
      ]
    ]);

    $_entity = $this->applyDtoAttributesToEntity($dtoMock, $entity);

    $this->verifyExpectedAttributes($_entity, 'nu3_36', 'config', [
      'global' => [
        'status' => 'approved',
        'name' => 'some name'
      ]
    ]);
  }

  function it_should_ignore_unknown_property(TransferObject $dto, Product $entity)
  {
    $dtoMock = $this->mockDto($dto, 'nu3_36', 'config', [
      'global' => [
        'status' => 'approved',
        'name' => 'some name',
        'unknown' => 'value'
      ]
    ]);

    $_entity = $this->applyDtoAttributesToEntity($dtoMock, $entity);

    $this->verifyExpectedAttributes($_entity, 'nu3_36', 'config', [
      'global' => [
        'status' => 'approved',
        'name' => 'some name'
      ]
    ]);
  }

  private function mockDto(TransferObject $dto, string $sku, string $type, array $properties)
  {
    $dto->getSku()->willReturn($sku);
    $dto->getType()->willReturn($type);
    $dto->getProductProperties()->willReturn($properties);

    return $dto;
  }

  private function verifyExpectedAttributes(
    Product $entity,
    string $sku,
    string $type,
    array $expectedProperties
  ) {
    $entity->__get('sku')->shouldReturn($sku);
    $entity->__get('type')->shouldReturn($type);
    $entity->__get('properties')->shouldReturn($expectedProperties);
  }
}

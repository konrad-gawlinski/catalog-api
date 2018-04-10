<?php

namespace spec\Product\Nu3\Service\Product;

use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\TransferObject;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EntityBuilderSpec extends ObjectBehavior
{
  function it_should_apply_dto_attributes_to_entity(TransferObject $dto, Product $product)
  {
    $dtoMock = $this->mockDto($dto, 'nu3_36', 'config', [
      'global' => [
        'status' => 'approved',
        'name' => 'some name'
      ]
    ]);

    $_entity = $this->applyDtoAttributesToEntity($dtoMock, $product);

    $this->verifyExpectedAttributes($_entity, 'nu3_36', 'config', [
      'global' => [
        'status' => 'approved',
        'name' => 'some name'
      ]
    ]);
  }

  function it_should_ignore_unknown_property(TransferObject $dto, Product $product)
  {
    $dtoMock = $this->mockDto($dto, 'nu3_36', 'config', [
      'global' => [
        'status' => 'approved',
        'name' => 'some name',
        'unknown' => 'value'
      ]
    ]);

    $_product = $this->applyDtoAttributesToEntity($dtoMock, $product);

    $this->verifyExpectedAttributes($_product, 'nu3_36', 'config', [
      'global' => [
        'status' => 'approved',
        'name' => 'some name'
      ]
    ]);
  }

  private function mockDto(TransferObject $dto, string $sku, string $type, array $properties)
  {
    $dto->sku = $sku;
    $dto->type = $type;
    $dto->properties = $properties;

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

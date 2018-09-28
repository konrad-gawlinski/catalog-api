<?php

namespace spec\Product\Nu3\Service\Product;

use Nu3\Service\Product\Entity\Product;
use Nu3\Service\Product\TransferObject;
use Nu3\Core\RegionUtils;
use Nu3\Service\Product\PropertyMap;
use Nu3\Spec\App;
use PhpSpec\ObjectBehavior;

class EntityBuilderSpec extends ObjectBehavior
{
  function let()
  {
    $this->setConfig(App::getInstance()->getConfig());
    $this->setPropertyMap(new PropertyMap());
    $this->setRegionUtils(new RegionUtils());
  }

  function it_should_apply_dto_attributes_to_entity(TransferObject $dto, Product $product)
  {
    $dtoMock = $this->mockDto($dto, 12, 'sku_36', 'config', [
      'global' => [
        'name' => 'some name'
      ]
    ]);

    $productEntity = $this->applyDtoAttributesToEntity($dtoMock, $product);

    $this->verifyExpectedAttributes($productEntity, 12, 'sku_36', 'config', [
      'global' => [
        'name' => 'some name'
      ]
    ]);
  }

  function it_should_ignore_unknown_property(TransferObject $dto, Product $product)
  {
    $dtoMock = $this->mockDto($dto, 12, 'sku_36', 'config', [
      'global' => [
        'name' => 'some name',
        'unknown' => 'value'
      ]
    ]);

    $_product = $this->applyDtoAttributesToEntity($dtoMock, $product);

    $this->verifyExpectedAttributes($_product, 12, 'sku_36', 'config', [
      'global' => [
        'name' => 'some name'
      ]
    ]);
  }

  function it_should_ignore_region_specific_property(TransferObject $dto, Product $product)
  {
    $dtoMock = $this->mockDto($dto, 12, 'sku_36', 'config', [
      'de_de' => [
        'name' => 'some name',
        'status' => 'new' //it is not allowed to set status for language regions
      ]
    ]);

    $_product = $this->applyDtoAttributesToEntity($dtoMock, $product);

    $this->verifyExpectedAttributes($_product, 12, 'sku_36', 'config', [
      'de_de' => [
        'name' => 'some name'
      ]
    ]);
  }

  function it_should_create_product_entity_from_product_array()
  {
    $productEntity = $this->createEntityFromProductArray([
      'id' => 12,
      'sku' => 'sku_36',
      'type' => 'simple',
      'de_de' => '{"name": "Product name"}'
    ]);

    $this->verifyExpectedAttributes($productEntity, 12, 'sku_36', 'simple', [
      'de_de' => [
        'name' => 'Product name'
      ]
    ]);
  }

  private function mockDto(TransferObject $dto, int $id, string $sku, string $type, array $properties)
  {
    $dto->id = $id;
    $dto->sku = $sku;
    $dto->type = $type;
    $dto->properties = $properties;

    return $dto;
  }

  private function verifyExpectedAttributes(
    Product $entity,
    int $id,
    string $sku,
    string $type,
    array $expectedProperties
  ) {
    $entity->__get('id')->shouldReturn($id);
    $entity->__get('sku')->shouldReturn($sku);
    $entity->__get('type')->shouldReturn($type);
    $entity->__get('properties')->shouldReturn($expectedProperties);
  }
}

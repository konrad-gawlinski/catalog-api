<?php

namespace Nu3\Service\Product;

use Nu3\Feature\PropertyMap as PropertyMapFeature;
use Nu3\Feature\RegionUtils as RegionUtilsFeature;
use Nu3\Service\Product\Entity\Product as ProductEntity;

class EntityBuilder
{
  private const REGION_ANY = 'any';
  private const REGION_GLOBAL = 'global';
  private const REGION_COUNTRY = 'country';
  private const REGION_LANGUAGE = 'language';

  use PropertyMapFeature;
  use RegionUtilsFeature;

  function applyDtoAttributesToEntity(TransferObject $dto, ProductEntity $entity) : ProductEntity
  {
    $entity->id = $entity->id ?: $dto->id;
    $entity->sku = $entity->sku ?: $dto->sku;
    $entity->type = $entity->type ?: $dto->type;

    $this->applyAttributesFromDtoToEntity($dto, $entity);

    return $entity;
  }

  private function applyAttributesFromDtoToEntity(TransferObject $dto, ProductEntity $entity)
  {
    $attributesMap = [
      self::REGION_ANY => array_flip($this->propertyMap()->db2Dto_AnyRegion()),
      self::REGION_GLOBAL => array_flip($this->propertyMap()->db2Dto_GlobalRegion()),
      self::REGION_COUNTRY => array_flip($this->propertyMap()->db2Dto_CountryRegion()),
      self::REGION_LANGUAGE => array_flip($this->propertyMap()->db2Dto_LanguageRegion())
    ];

    $this->applyAttributes($dto->properties, $entity->properties, $attributesMap);
  }

  function applyEntityAttributesToDto(ProductEntity $entity, TransferObject $dto) : TransferObject
  {
    $dto->id = $entity->id ?: $dto->id;
    $dto->sku = $entity->sku ?: $dto->sku;
    $dto->type = $entity->type ?: $dto->type;

    $this->applyAttributesFromEntityToDto($entity, $dto);

    return $dto;
  }

  private function applyAttributesFromEntityToDto(ProductEntity $entity, TransferObject $dto)
  {
    $attributesMap = [
      self::REGION_ANY => $this->propertyMap()->db2Dto_AnyRegion(),
      self::REGION_GLOBAL => $this->propertyMap()->db2Dto_GlobalRegion(),
      self::REGION_COUNTRY => $this->propertyMap()->db2Dto_CountryRegion(),
      self::REGION_LANGUAGE => $this->propertyMap()->db2Dto_LanguageRegion()
    ];

    $this->applyAttributes($entity->properties, $dto->properties, $attributesMap);
  }

  private function applyAttributes(array $source, array &$target, array $attributesMap)
  {
    $attributesMapAny = $attributesMap[self::REGION_ANY];

    foreach ($source as $region => $regionProperties) {
      if ($regionProperties) {
        $regionSpecificMap = $this->pickRegionSpecificMap($region, $attributesMap);

        foreach ($regionProperties as $property => $value) {
          if (isset($regionSpecificMap[$property]))
            $target[$region][$regionSpecificMap[$property]] = $value;

          else if (isset($attributesMapAny[$property]))
            $target[$region][$attributesMapAny[$property]] = $value;
        }
      }
    }
  }

  private function pickRegionSpecificMap(string $region, array $attributesMap) : array
  {
    if ($this->regionUtils()->isGlobal($region)) return $attributesMap[self::REGION_GLOBAL];
    if ($this->regionUtils()->isCountry($region)) return $attributesMap[self::REGION_COUNTRY];
    if ($this->regionUtils()->isLanguage($region)) return $attributesMap[self::REGION_LANGUAGE];

    return [];
  }

  function createEntityFromProductArray(array $productArray) : ProductEntity
  {
    $entity = new ProductEntity();
    $this->fillEntityFromProductArray($entity, $productArray);

    return $entity;
  }

  private function fillEntityFromProductArray(ProductEntity $entity, array $input)
  {
    $scalarProperties = [Property::PRODUCT_ID, Property::PRODUCT_SKU, Property::PRODUCT_TYPE];

    $entity->id = $input[Property::PRODUCT_ID];
    $entity->sku = $input[Property::PRODUCT_SKU];
    $entity->type = $input[Property::PRODUCT_TYPE];

    foreach ($input as $propertyName => $value) {
      if (!in_array($propertyName, $scalarProperties) && !empty($value)) {
        $entity->properties[$propertyName] = json_decode($value, true);
      }
    }
  }
}

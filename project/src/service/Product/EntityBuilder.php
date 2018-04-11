<?php

namespace Nu3\Service\Product;

use Nu3\Config;
use Nu3\Feature\Config as ConfigFeature;
use Nu3\Feature\PropertyMap as PropertyMapFeature;
use Nu3\Feature\RegionCheck as RegionCheckFeature;
use Nu3\Service\Product\Entity\Product;

class EntityBuilder
{
  private const REGION_ANY = 'any';
  private const REGION_GLOBAL = 'global';
  private const REGION_COUNTRY = 'country';
  private const REGION_LANGUAGE = 'language';

  use ConfigFeature;
  use PropertyMapFeature;
  use RegionCheckFeature;

  function applyDtoAttributesToEntity(TransferObject $dto, Product $entity) : Product
  {
    $entity->sku = $entity->sku ?: $dto->sku;
    $entity->type = $entity->type ?: $dto->type;

    $this->applyAttributesFromDtoToEntity($dto, $entity);

    return $entity;
  }

  private function applyAttributesFromDtoToEntity(TransferObject $dto, Product $entity)
  {
    $attributesMap = [
      self::REGION_ANY => array_flip($this->propertyMap()->db2Dto_AnyRegion()),
      self::REGION_GLOBAL => array_flip($this->propertyMap()->db2Dto_GlobalRegion()),
      self::REGION_COUNTRY => array_flip($this->propertyMap()->db2Dto_CountryRegion()),
      self::REGION_LANGUAGE => array_flip($this->propertyMap()->db2Dto_LanguageRegion())
    ];

    $this->applyAttributes($dto->properties, $entity->properties, $attributesMap);
  }

  function applyEntityAttributesToDto(Product $entity, TransferObject $dto) : TransferObject
  {
    $dto->sku = $entity->sku ?: $dto->sku;
    $dto->type = $entity->type ?: $dto->type;

    $this->applyAttributesFromEntityToDto($entity, $dto);

    return $dto;
  }

  private function applyAttributesFromEntityToDto(Product $entity, TransferObject $dto)
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
    if ($this->regionCheck()->isGlobal($region)) return $attributesMap[self::REGION_GLOBAL];
    if ($this->regionCheck()->isCountry($region)) return $attributesMap[self::REGION_COUNTRY];
    if ($this->regionCheck()->isLanguage($region)) return $attributesMap[self::REGION_LANGUAGE];

    return [];
  }

  function fillEntityFromDbArray(Product $entity, array $input)
  {
    $entity->id = $input[Property::PRODUCT_ID];
    $entity->sku = $input[Property::PRODUCT_SKU];
    $entity->type = $input[Property::PRODUCT_TYPE];
    foreach ($this->getRegionNames() as $region) {
      $entity->properties[$region] = json_decode($input[$region], true);
    }
  }

  private function getRegionNames()
  {
    $config = $this->config()[Config::REGION];

    return array_merge(
      $config[Config::GLOBAL_REGION],
      $config[Config::COUNTRY_REGION],
      $config[Config::LANGUAGE_REGION]
    );
  }
}

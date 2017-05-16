<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Entity\Product as ProductEntity;

class ProductSaveFactory
{
  use \Nu3\Feature\Config;

  function createDataTransferObject(Request\ProductSave $request) : DTO\ProductSave
  {
    return new DTO\ProductSave($request);
  }

  function createValidator() : ProductSaveValidator
  {
    $object = new ProductSaveValidator();
    $object->setConfig($this->config());

    return $object;
  }

  function createProductBuilder() : ProductBuilder
  {
    return new ProductBuilder($this->config());
  }

  function createProductEntityFromDto(DTO\ProductSave $dto) : ProductEntity
  {
    $entity = new ProductEntity();
    $entity->fillFromDto($dto);

    return $entity;
  }

  function createEntityValidator() : EntityValidator
  {
    $validator = new EntityValidator();
    $validator->setConfig($this->config());

    return $validator;
  }

  function createPropertyValueFilter() : PropertyValueFilter
  {
    return new PropertyValueFilter();
  }
}

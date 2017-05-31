<?php

namespace Nu3\Service\Product\SaveAction;

use Nu3\Service\Product\Entity\Product as ProductEntity;

class Factory
{
  use \Nu3\Feature\Config;

  function createDataTransferObject(Request $request) : TransferObject
  {
    return new TransferObject($request);
  }

  function createValidator() : Validator
  {
    $object = new Validator();
    $object->setConfig($this->config());

    return $object;
  }

  function createProductBuilder() : ProductBuilder
  {
    return new ProductBuilder($this->config());
  }

  function createProductEntityFromDto(TransferObject $dto) : ProductEntity
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

  function createValueFilter() : ValueFilter
  {
    return new ValueFilter();
  }
}

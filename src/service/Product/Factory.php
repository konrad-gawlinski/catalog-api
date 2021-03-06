<?php

namespace Nu3\Service\Product;

use Nu3\Core\Database\QueryRunner\Product as ProductGateway;

class Factory
{
  use \Nu3\Feature\Config;
  use \Nu3\Feature\DatabaseConnection;
  use \Nu3\Feature\PropertyMap;
  use \Nu3\Feature\RegionUtils;

  function createDataTransferObject()
  {
    return new TransferObject();
  }

  function createProductGateway() : ProductGateway
  {
    return new ProductGateway($this->databaseConnection());
  }

  function createProductEntity() : Entity\Product
  {
    return new Entity\Product();
  }

  /**
   * @return EntityBuilder
   */
  function createEntityBuilder()
  {
    $entityBuilder =  new EntityBuilder();
    $entityBuilder->setPropertyMap($this->propertyMap());
    $entityBuilder->setRegionUtils($this->regionUtils());
    
    return $entityBuilder;
  }

  function createEntityValidator() : Entity\Validator
  {
    $validator = new Entity\Validator();
    $validator->setConfig($this->config());

    return $validator;
  }

  function createValueFilter() : ValueFilter
  {
    return new ValueFilter();
  }

  function createProductValidator() : Validator\ProductValidator
  {
    $object = new Validator\ProductValidator($this);
    $object->setConfig($this->config());
    $object->setRegionUtils($this->regionUtils());

    return $object;
  }

  function createProductSimpleValidator() : Validator\ProductSimple
  {
    return new Validator\ProductSimple($this);
  }
}

<?php

namespace Nu3\Service\Product;

use Nu3\Core\Database\Gateway\Product as ProductGateway;

class Factory
{
  use \Nu3\Feature\Config;
  use \Nu3\Feature\DatabaseConnection;
  use \Nu3\Feature\PropertyMap;
  use \Nu3\Feature\RegionUtils;

  function createDataTransferObject() : TransferObject
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
    $entityBuilder->setConfig($this->config());
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
}

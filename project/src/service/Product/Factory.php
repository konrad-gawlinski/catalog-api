<?php

namespace Nu3\Service\Product;

use Nu3\Core\Database\Gateway\Product as DatabaseGateway;

class Factory
{
  use \Nu3\Feature\Config;
  use \Nu3\Feature\DatabaseConnection;

  function createDataTransferObject() : TransferObject
  {
    return new TransferObject();
  }

  function createProductGateway() : DatabaseGateway
  {
    return new DatabaseGateway($this->databaseConnection());
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

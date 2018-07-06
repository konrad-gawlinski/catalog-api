<?php

namespace Nu3\Service\Product\Action\CreateProduct;

class Factory extends \Nu3\Service\Product\Factory
{
  /**
   * @return Builder
   */
  function createEntityBuilder()
  {
    $entityBuilder =  new Builder();
    $entityBuilder->setConfig($this->config());
    $entityBuilder->setPropertyMap($this->propertyMap());
    $entityBuilder->setRegionCheck($this->regionCheck());

    return $entityBuilder;
  }

  function createRequestValidator() : RequestValidator
  {
    $object = new RequestValidator();
    $object->setConfig($this->config());

    return $object;
  }
}

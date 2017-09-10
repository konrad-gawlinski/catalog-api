<?php

namespace Nu3\Service\Product\Action\GetProduct;

use Nu3\Service\Product\Entity\Product as ProductEntity;

class Factory extends \Nu3\Service\Product\Factory
{
  function createValidator() : Validator
  {
    $object = new Validator();
    $object->setConfig($this->config());

    return $object;
  }

  function createProductEntityFromDB(array $product) : ProductEntity
  {
    $entity = new ProductEntity();
    $entity->fillFromDb($product);

    return $entity;
  }

  function createProductResponse(ProductEntity $product) : Response
  {
    return new Response($product);
  }
}

<?php

namespace Nu3\Service\Product\GetAction;

use Nu3\Service\Product\Entity\Product as ProductEntity;

class Factory
{
  use \Nu3\Feature\Config;

  function createValidator() : Validator
  {
    $object = new Validator();
    $object->setConfig($this->config());

    return $object;
  }

  function createProductEntityFromDB(array $product) : ProductEntity
  {
    $entity = new ProductEntity();
    $entity->fillFromArray($product);

    return $entity;
  }

  function createProductResponse(ProductEntity $product) : Response
  {
    return new Response($product);
  }
}

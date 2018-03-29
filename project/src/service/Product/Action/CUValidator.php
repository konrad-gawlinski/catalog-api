<?php

namespace Nu3\Service\Product\Action;

use Nu3\Core\Database\Gateway\Product as ProductGateway;

/**
 * Create/Update Validator
 */
abstract class CUValidator implements Validator
{
  /** @var ProductGateway */
  protected $productGateway;

  function setProductGateway(ProductGateway $productGateway)
  {
    $this->productGateway = $productGateway;
  }
}

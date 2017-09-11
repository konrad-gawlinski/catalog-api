<?php

namespace Nu3\Service\Product\Action;

use Nu3\Service\Product\Factory;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Service\Kernel\ViolationsTranslator;

abstract class ActionBase
{
  use ViolationsTranslator;

  /** @var Factory */
  protected $factory;

  /** @var ProductGateway */
  protected $dbGateway;

  function __construct(Factory $factory)
  {
    http_response_code(500);
    
    $this->dbGateway = $factory->createDatabaseGateway();
  }
}

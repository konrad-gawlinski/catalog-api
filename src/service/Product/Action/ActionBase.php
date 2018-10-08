<?php

namespace Nu3\Service\Product\Action;

use Nu3\Service\Product\Factory;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Service\Kernel\ViolationsTranslator;
use Nu3\Core\Violation;

abstract class ActionBase
{
  use ViolationsTranslator;

  /** @var Factory */
  protected $factory;

  /** @var ProductGateway */
  protected $productGateway;

  function __construct(Factory $factory)
  {
    http_response_code(500);
    
    $this->productGateway = $factory->createProductGateway();
    $this->factory = $factory;
  }

  /**
   * @param Violation[] $violations
   * @return int
   */
  protected function returnHttpStatusCode(array $violations) : int
  {
    if (count($violations) > 1) {
      return 400;
    }

    return $this->errorKey2HttpCode($violations[0]->errorKey());
  }

  abstract protected function errorKey2HttpCode(string $errorKey);
}

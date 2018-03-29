<?php

namespace Nu3\Service\Product\Action\CreateProduct;

use Nu3\Service\Product\Request as BaseRequest;
use Nu3\Service\Product\Feature\RequestPayload;

/**
 * Create/Update Request
 */
class Request extends BaseRequest
{
  use RequestPayload;
}

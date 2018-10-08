<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Service\Product\Feature\RequestId;
use Nu3\Service\Product\Feature\RequestPayload;
use Nu3\Service\Product\Request as BaseRequest;

class Request extends BaseRequest
{
  use RequestId;
  use RequestPayload;
}

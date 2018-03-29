<?php

namespace Nu3\Service\Product\Action;

use Nu3\Service\Product\Feature\RequestPayload;
use Nu3\Service\Product\TransferObject;

class Factory extends \Nu3\Service\Product\Factory
{
  /**
   * @param RequestPayload $request
   */
  function createDataTransferObject($request) : TransferObject
  {
    return new TransferObject($request);
  }
}

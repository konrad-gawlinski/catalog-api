<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Service\Product\TransferObject as ProductTransferObject;

class TransferObject extends ProductTransferObject
{
  /** @var int */
  public $id = 0;

  /**
   * @param Request $request
   */
  function applyRequestProperties($request)
  {
    parent::applyRequestProperties($request);
    $this->id = $request->getId();
  }
}

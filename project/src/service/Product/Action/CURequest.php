<?php

namespace Nu3\Service\Product\Action;

use Nu3\Service\Product\Request;

/**
 * Create/Update Request
 */
class CURequest extends Request
{
  const PROPERTY_PAYLOAD = 'payload';
  const PROPERTY_SKU = 'sku';

  function getPayload()
  {
    return $this->getValue(self::PROPERTY_PAYLOAD);
  }

  function getSku(): string
  {
    return $this->getValue(self::PROPERTY_SKU);
  }
}

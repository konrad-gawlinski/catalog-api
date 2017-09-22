<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Action\CURequest;

class TransferObject
{
  /** @var string */
  private $sku;

  /** @var string */
  private $country;

  /** @var string */
  private $language;

  /** @var array */
  private $productProperties;

  function __construct(CURequest $request)
  {
    $this->sku = $request->getSku();
    $this->country = $request->getCountry();
    $this->language = $request->getLanguage();
    $this->productProperties = $request->getPayload();
  }

  function getProductProperties() : array
  {
    return $this->productProperties;
  }

  function getSku() : string
  {
    return $this->sku;
  }

  function getCountry() : string
  {
    return $this->country;
  }

  function getLanguage() : string
  {
    return $this->language;
  }

}

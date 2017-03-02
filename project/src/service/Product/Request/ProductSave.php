<?php

namespace Nu3\Service\Product\Request;

use Nu3\Core\Violation;
use Nu3\Service\Product\Entity\Properties as Property;

class ProductSave
{
  private $storedProduct = [];
  private $violations = [];
  private $payload;
  private $validator;

  function __construct(string $json, Validator $validator)
  {
    $this->payload = json_decode($json, true);
    $this->validator = $validator;
  }

  function getPayload() : array
  {
    return $this->payload;
  }

  function getPayloadProduct() : array
  {
    return $this->payload[Property::PRODUCT];
  }

  function getPayloadStorage() : string
  {
    return $this->payload[Property::STORAGE];
  }

  function setStoredProduct(array $product)
  {
    $this->storedProduct = $product;
  }

  function getStoredProduct() : array
  {
    return $this->storedProduct;
  }

  function getViolations() : array
  {
    return $this->violations;
  }

  /**
   * @return Violation[]
   */
  function validatePayload() : array
  {
    $violations = $this->validator->validatePayload($this);
    $this->violations += $violations;

    return $violations;
  }

  /**
   * @return Violation[]
   */
  function validateProduct() : array
  {
    $violations = $this->validator->validateProduct($this);
    $this->violations += $violations;

    return $violations;
  }
}

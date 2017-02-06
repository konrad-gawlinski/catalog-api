<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Entity\Payload;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nu3\Core;
use Nu3\Property;
use Nu3\Service\Product\Entity\Properties as ProductProperty;

class Controller
{
  function save(Request $request, Model $productModel): Response
  {
    $violations = [];
    $result = $this->init($productModel, $this->getInput());

    if ($result->isValid()) {
      $payloadEntity = $this->buildPayload($result->getPayload());
      $productEntity = $productModel->createProductEntity($payloadEntity, $result->getStoredProduct());
      $productModel->validateEntity($productEntity);

      $productModel->saveProduct($productEntity);
    } else {
      $violations = $result->getViolations();
    }

    var_dump('Violations: ', $violations);
    return new Response('Product save action', 200);
  }

  private function init(Model $productModel, string $json) : InitRequest
  {
    $storedProduct = [];
    $payload = json_decode($json, true);

    do {
      $violations = $productModel->preValidatePayload($payload);
      if (isset($violations[0])) break;

      $storedProduct = $productModel->fetchProductType(
        $payload[ProductProperty::PRODUCT][ProductProperty::PRODUCT_SKU],
        $payload[ProductProperty::STORAGE]
      );

      $violations = $productModel->preValidateProduct($payload, $storedProduct);
    } while(false);

    return new InitRequest($storedProduct, $violations, $payload);
  }

  private function buildPayload(array $input) : Payload
  {
    $payload = new Payload();
    $payload->product = $input[ProductProperty::PRODUCT];
    $payload->storage = $input[ProductProperty::STORAGE];

    return $payload;
  }

  private function getInput(): string
  {
    return <<<'PAYLOAD'
{
  "storage": "catalog",
  "product": {
    "sku": "nu3_1",
    "status": "new",
    "name": " Silly Hodgin",
    "type": "config",
    "price": {
      "final": 5172
    },
    "tax_rate": 19,
    "attributes": [
      "is_gluten_free",
      "is_lactose_free"
    ],
    "seo": {
      "robots": ["noindex", "follow"],
      "title": "Silly Hodgkin "
    },
    "manufacturer": "philips2",
    "description": "Your neighbours will visit you more often",
    "short_description": "curved 55\" tv",
    "manufacturer": "philips",
    "label_language": [
      "en",
      "it"
    ]
  }
}
PAYLOAD;
  }
}

class InitRequest
{
  private $storedProduct;
  private $violations;
  private $payload;

  function __construct(array $storedProduct, array $violations, array $payload)
  {
    $this->storedProduct = $storedProduct;
    $this->violations = $violations;
    $this->payload = $payload;
  }

  function isValid() : bool
  {
    return empty($this->violations);
  }

  function getViolations() : array
  {
    return $this->violations;
  }

  function getStoredProduct() : array
  {
    return $this->storedProduct;
  }

  function getPayload() : array
  {
    return $this->payload;
  }
}

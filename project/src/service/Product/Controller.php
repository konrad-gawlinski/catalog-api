<?php

namespace Nu3\Service\Product;

use Nu3\Service\Product\Entity\Payload;
use Nu3\Service\Product\Entity\ProductStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nu3\Core;
use Nu3\Property;
use Nu3\Service\Product\Entity\Properties as ProductProperty;

class Controller
{
  function save(Request $request, Model $productModel): Response
  {
    $json = $this->getInput();
    $input = json_decode($json, true);

    if (!empty($input[ProductProperty::PRODUCT][ProductProperty::PRODUCT_SKU])) {
      $product = $input[ProductProperty::PRODUCT];
      $sku = $product[ProductProperty::PRODUCT_SKU];
      $type = empty($product[ProductProperty::PRODUCT_TYPE]) ? '' : $product[ProductProperty::PRODUCT_TYPE];
      $status = empty($product[ProductProperty::PRODUCT_STATUS]) ? $product[ProductProperty::PRODUCT_STATUS] : ProductStatus::NEW;

      $productModel->set($sku, $type);
      $productModel->validateSchema($input);
      $payload = $this->buildPayload($input);
      $productModel->validate($payload);

      $db = $productModel->getDatabaseProductBroker();
      $db->set_schema(Core\Database\Connection::SCHEMA_CATALOG);
      $db->save_product($sku, $status, $productModel->prepareProductForDb($product));
      $db->disconnect();

    } else {
      //Todo error message
    }

    return new Response('Product save action', 200);
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
  "storage": "common",
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
      " en ",
      " it"
    ]
  }
}
PAYLOAD;
  }
}
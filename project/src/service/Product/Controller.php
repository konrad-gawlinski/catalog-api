<?php

namespace Nu3\Service\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nu3\Config as Nu3Config;
use Nu3\Core;
use Nu3\Property;
use Nu3\Service\Product\Entity\Properties as ProductProperty;

class Controller
{
  use Property\Config;

  function save(Request $request, Model $productModel): Response
  {
    $json = $this->getInput();
    $payload = json_decode($json, true);

    if (!empty($payload[ProductProperty::PRODUCT][ProductProperty::PRODUCT_SKU])) {
      $product = $payload[ProductProperty::PRODUCT];
      $sku = $product[ProductProperty::PRODUCT_SKU];
      $type = empty($product[ProductProperty::PRODUCT_TYPE]) ? '' : $product[ProductProperty::PRODUCT_TYPE];

      $productModel->set($sku, $type);
      $productModel->validateSchema($payload);
      $payload = $productModel->deserialize($json, $payload[ProductProperty::PRODUCT]);

      var_dump($payload);
      $productModel->validate($payload);

      var_dump('Config : ' . $this->config()[Nu3Config::DB][Nu3Config::DB_HOST]);
    } else {
      //Todo error message
    }

    return new Response('Product save action', 200);
  }

  private function getInput(): string
  {
    return <<<'PAYLOAD'
{
  "storage": "COMMON",
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
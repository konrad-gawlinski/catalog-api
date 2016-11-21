<?php

namespace Nu3\Service\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nu3\Config as Nu3Config;
use Nu3\Service\Product\Serializer as ProductSerializer;
use Nu3\Property;

class Controller
{
  use Property\Config;

  function save(Request $request, ProductSerializer $serializer): Response
  {
    $product = $serializer->deserialize($this->getInput());
    var_dump('Config : '. $this->config()[Nu3Config::DB][Nu3Config::DB_HOST]);

    return new Response('Product save action', 200);
  }

  private function getInput(): string
  {
    return <<<'PAYLOAD'
{
 "sku": "nu3_1",
 "target": "COMMON",
 "properties": {
   "name": "silly_hodgkin",
   "type": "book",
   "price": 5172,
   "tax_rate": 19,
   "attributes": [
     "is_gluten_free",
     "is_lactose_free"
   ],
   "seo_robots": [
     "noindex",
     "follow"
   ],
   "manufacturer": "philips",
   "label_language": [
     "en",
     "it"
   ],
   "ingredient_list": 1
 }
}
PAYLOAD;
  }
}
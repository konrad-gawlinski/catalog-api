<?php

namespace Nu3\Service\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nu3\Config as Nu3Config;
use Nu3\Property;

class Controller
{
  use Property\Config;
  /**
   * @return Response
   */
  function save(Request $request, \JsonSchema\Validator $schemaValidator): Response
  {
    $payload = json_decode($this->getInput());
    $schema = $this->getSchema();

    $schemaValidator->check($payload, $schema);
    var_dump($schemaValidator->getErrors());
    var_dump($this->config()[Nu3Config::DB][Nu3Config::DB_HOST]);

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

  private function getSchema()
  {
    return json_decode(file_get_contents(__DIR__.'/schema-save.json'));
  }
}
<?php
namespace Nu3\Service;

use Symfony\Component\HttpFoundation\Response;

class Product
{
  /**
   * Sample json input
   * {
   *  "name": "silly_hodgkin",
   *  "type": "book",
   *  "price": 5172,
   *  "variety": "config",
   *  "tax_rate": 19,
   *  "attributes": [
   *    "is_gluten_free",
   *    "is_lactose_free"
   *  ],
   *  "seo_robots": [
   *    "noindex",
   *    "follow"
   *  ],
   *  "manufacturer": "philips",
   *  "label_language": [
   *    "en",
   *    "it"
   *  ],
   *  "ingredient_list": 1
   * }
   *
   * @return Response
   */
  function save(): Response
  {
    $payload = json_decode($this->getInput());
    $schema = json_decode($this->getSchema());

    // Validate
    $validator = new \JsonSchema\Validator();
    $validator->check($payload, $schema);
    var_dump($validator->getErrors());

    return new Response('Product save action', 200);
  }

  private function getInput(): string
  {
    return <<<'PAYLOAD'
{
 "name": "silly_hodgkin",
 "type": "book",
 "price": 5172,
 "variety": "config",
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
PAYLOAD;
  }

  private function getSchema()
  {
    return <<<'SCHEMA'
{
  "title": "Example Schema",
  "type": "object",
  "properties": {
    "name": {
      "type": "string"
    },
    "type": {
      "type": "string"
    }
  },
  "required": ["name"]
}    
SCHEMA;
  }
}
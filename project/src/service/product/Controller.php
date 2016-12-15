<?php

namespace Nu3\Service\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nu3\Config as Nu3Config;
use Nu3\Service\Product\Serializer as ProductSerializer;
use Nu3\Service\Product\Validator as ProductValidator;
use Nu3\Property;
use Symfony\Component\Validator\ConstraintViolation;

class Controller
{
  use Property\Config;

  /** @var  JsonValidator */
  private $jsonValidator;

  /** @var  ProductSerializer */
  private $serializer;

  /** @var  ProductValidator */
  private $validator;

  function setJsonValidator(JsonValidator $jsonValidator)
  {
    $this->jsonValidator = $jsonValidator;
  }

  function setSerializer(ProductSerializer $serializer)
  {
    $this->serializer = $serializer;
  }

  function setValidator(ProductValidator $validator)
  {
    $this->validator = $validator;
  }


  function save(Request $request): Response
  {
    $this->jsonValidator->validate($this->getInput());
    $product = $this->serializer->deserialize($this->getInput());
    var_dump($product);
//    $violations = $this->validator->validate($product);
//
//    /** @var ConstraintViolation $violation */
//    foreach ($violations as $violation) {
//      var_dump($violation->getMessage());
//    }
    var_dump('Config : '. $this->config()[Nu3Config::DB][Nu3Config::DB_HOST]);

    return new Response('Product save action', 200);
  }

  private function getInput(): string
  {
    return <<<'PAYLOAD'
{
  "storage": "COMMON",
  "product": {
    "sku": "nu3_1",
    "name": "silly_hodgkin",
    "type": "book",
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
      "title": "Silly Hodgkin"
    },
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
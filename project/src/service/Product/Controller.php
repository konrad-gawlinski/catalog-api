<?php

namespace Nu3\Service\Product;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nu3\Config as Nu3Config;
use Nu3\Service\Product\Entity\Payload;
use Nu3\Core;
use DMS\Filter\Filter;
use Nu3\Property;
use Symfony\Component\Validator\ConstraintViolation;

class Controller
{
  use Property\Config;

  /** @var Core\JsonValidator */
  private $jsonValidator;

  /** @var Core\Serializer */
  private $serializer;

  /** @var Core\Validator */
  private $validator;

  /** @var Filter */
  private $sanitizer;

  function setJsonValidator(Core\JsonValidator $jsonValidator)
  {
    $this->jsonValidator = $jsonValidator;
  }

  function setSerializer(Core\Serializer $serializer)
  {
    $this->serializer = $serializer;
  }

  function setValidator(Core\Validator $validator)
  {
    $this->validator = $validator;
  }

  function setSanitizer(Filter $sanitizer)
  {
    $this->sanitizer = $sanitizer;
  }

  function save(Request $request): Response
  {
    $jsonSchema = APPLICATION_SRC . 'service/Product/config/validation-schema.json';
    $this->jsonValidator->validate($this->getInput(), $jsonSchema);

    /** @var Payload $payload */
    $payload = $this->serializer->deserialize($this->getInput(), Payload::class);
    $this->sanitizer->filterEntity($payload->product);
    $this->sanitizer->filterEntity($payload->product->seo);
    $this->sanitizer->filterEntity($payload->product->price);

    var_dump($payload);
    $violations = $this->validator->validate($payload);

    /** @var ConstraintViolation $violation */
    foreach ($violations as $violation) {
      var_dump($violation->getMessage());
    }

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
    "status": "new",
    "name": " Silly Hodgin",
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
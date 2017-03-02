<?php
use Symfony\Component\HttpFoundation\Request;

function getPayload()
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

$app->get('/product/save', function(Request $request) use($app) {
  /** @var Nu3\Service\Product\SaveAction $service */
  $service = $app['service.product'];
  $productSaveRequest = new \Nu3\Service\Product\Request\ProductSave(
    getPayload(),
    $app['product.save_request.validator']);

  return $service->run($productSaveRequest, $app['product.model'], new Nu3\Service\Product\ContentBuilder\Database());
});

<?php
use Symfony\Component\HttpFoundation\Request;

function getPayload()
{
  return <<<'PAYLOAD'
{
  "parent": null,
  "status": "new",
  "name": " Silly Hodgin",
  "type": "config",
  "final_price": 5172,
  "tax_rate": 19,
  "is_gluten_free": true,
  "is_lactose_free": true,
  "seo_robots": ["noindex", "follow"],
  "seo_title": "Silly Hodgkin",
  "manufacturer": "philips2",
  "description": "Your neighbours will visit you more often",
  "short_description": "curved 55\" tv",
  "manufacturer": "philips",
  "label_language": ["en", "it"]
}
PAYLOAD;
}

$app->put('/product/save/{sku}', function(Request $request, string $sku) use($app) {
  $payload = $request->request->all();

  /** @var Nu3\Service\Product\Action\UpdateProduct\Action $service */
  $service = $app['product.update_action'];
  $productSaveRequest = new \Nu3\Service\Product\Request($sku, $payload);

  return $service->run($productSaveRequest);
});

$app->post('/product/save/{sku}', function(Request $request, string $sku) use($app) {
  $payload = $request->request->all();

  /** @var Nu3\Service\Product\Action\CreateProduct\Action $service */
  $service = $app['product.create_action'];
  $productSaveRequest = new \Nu3\Service\Product\Request($sku, $payload);

  return $service->run($productSaveRequest);
});

$app->get('/product/{sku}/{country}/{lang}', function($sku, $country, $lang) use($app) {
  /** @var Nu3\Service\Product\GetAction\Action $service */
  $service = $app['product.get_action'];
  $productGetRequest = new \Nu3\Service\Product\GetAction\Request(['sku' => $sku, 'country' => $country, 'lang' => $lang]);

  return $service->run(
    $productGetRequest,
    $app['product.gateway']
  );
});

$app->get('/product/to_flat', function() use($app) {
  /** @var Nu3\Service\Product\ImportAction\Action $service */
  $service = new \Nu3\Service\Product\ImportAction\Action();

  return $service->create_flat_structure();
});

$app->get('/product/import', function() use($app) {
  /** @var Nu3\Service\Product\ImportAction\Action $service */
  $service = new \Nu3\Service\Product\ImportAction\Action();

  return $service->import();
});

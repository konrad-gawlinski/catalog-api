<?php
use Symfony\Component\HttpFoundation\Request;

$app->put('/product/save/{sku}',
  function(Request $request, string $sku, string $country, string $lang) use($app) {
    $payload = $request->request->all();

    /** @var Nu3\Service\Product\Action\UpdateProduct\Action $service */
    $service = $app['product.update_action'];
    $productSaveRequest = new \Nu3\Service\Product\Request($sku, $country, $lang, $payload);

    return $service->run($productSaveRequest);
  }
);

$app->post('/product/{sku}/{country}/{lang}',
  function(Request $request, string $sku, string $country, string $lang) use($app) {
    $payload = $request->request->all();

    /** @var Nu3\Service\Product\Action\CreateProduct\Action $service */
    $service = $app['product.create_action'];
    $productSaveRequest = new \Nu3\Service\Product\Request($sku, $country, $lang, $payload);

    return $service->run($productSaveRequest);
  }
);

$app->get('/product/{sku}/{country}/{lang}', function($sku, $country, $lang) use($app) {
  /** @var Nu3\Service\Product\Action\GetProduct\Action $service */
  $service = $app['product.get_action'];
  $productGetRequest = new \Nu3\Service\Product\Action\GetProduct\Request(['sku' => $sku, 'country' => $country, 'lang' => $lang]);

  return $service->run(
    $productGetRequest
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

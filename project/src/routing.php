<?php
use Symfony\Component\HttpFoundation\Request;

$app->get('/product/save', function(Request $request) use($app) {
  /** @var Nu3\Service\Product\Controller $service */
  $service = $app['service.product'];
  return $service->save($request, $app['product.serializer'], $app['product.validator']);
});

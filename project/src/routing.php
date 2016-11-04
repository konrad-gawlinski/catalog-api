<?php

$app->get('/product/save', function() use($app) {
  /** @var Nu3\Service\Product $service */
  $service = $app['service.product'];
  return $service->save();
});

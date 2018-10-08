<?php

class ProductCest
{
  private $randSku;
  private $productId = 0;

  function __construct()
  {
    $this->randSku = $this->generateRandomSku();
    echo "\nRandom sku used for the tests: [{$this->randSku}]\n";
  }

  private function generateRandomSku() : string
  {
    $maxRand = mt_getrandmax();
    return 'id_'. mt_rand($maxRand - 100000, $maxRand);
  }

  function it_should_fail_creating_product_missing_required_fields(Product_serviceTester $I)
  {
    $payload = $this->createProductJson();
    $payload = str_replace('"name": " Silly Hodgin",', '', $payload);
    $payload = str_replace('"final_gross_price": 5172,', '', $payload);

    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPOST("/product/create", $payload);
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
    $I->seeResponseContains(
      '["This field is missing. properties[de-de_de][name]"'.
      ',"This field is missing. properties[fr-fr_fr][name]"'.
      ',"This field is missing. properties[at-at_de][name]"'.
      ',"This field is missing. properties[at-de_de][name]"]'
    );
  }

  function it_should_succeed_creating_product(Product_serviceTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPOST("/product/create", $this->createProductJson());
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
    $I->seeResponseEquals('');

    preg_match('/\/(\d+)$/', $I->grabHttpHeader('Location'), $matches);
    $this->productId = intval($matches[1]);
  }

  function it_should_fail_creating_existing_product(Product_serviceTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPOST("/product/create", $this->createProductJson());
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
    $I->seeResponseEquals('["product_already_created"]');
  }

  function it_should_succeed_updating_product(Product_serviceTester $I)
  {
    $updateJson = <<<JSON
{
  "properties": {
    "global": {
      "name": "Mad Hodgin ",
      "final_gross_price": 699,
      "is_gluten_free": false,
      "manufacturer": "samsung"  
    }
  }
}
JSON;

    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPUT("/product/{$this->productId}", $updateJson);
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT);
    $I->seeResponseEquals('');
  }

  function it_should_succeed_getting_by_id(Product_serviceTester $I)
  {
    $I->sendGet("/product/{$this->productId}");
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    $I->seeResponseIsJson();

    $expectedResponse = $this->readProductJson($this->productId);
    $I->seeResponseEquals($expectedResponse);
  }

  function it_should_fail_getting_non_existing_id(Product_serviceTester $I)
  {
    $I->sendGet('/product/99994932432');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
    $I->seeResponseEquals('["product_not_found"]');
  }

  //this function has to be last as it is executed as the last one
  function clean_up(Product_serviceTester $I)
  {
    /** @var \Nu3\Core\Database\Connection $db */
    $db = $I->getApp()['database.connection'];
    $I->removeProductBySku($db->connectionRes(), $this->randSku);
  }

  private function createProductJson()
  {
    return <<<JSON
{
  "sku": "{$this->randSku}",
  "type": "simple",
  "properties": {
    "global": {
      "name": " Silly Hodgin",
      "type": "config",
      "final_gross_price": 5172,
      "is_gluten_free": true,
      "is_lactose_free": true,
      "seo_robots": ["noindex", "follow"],
      "seo_title": "Silly Hodgkin",
      "not_supported_attribute": null,
      "manufacturer": "philips2",
      "manufacturer": "philips",
      "label_language": ["en", "it"]
    },
    "de": {
      "name": " Silly Hodgin",
      "status": "new",
      "tax_rate": 19
    },
    "de_de": {
      "name": " Silly Hodgin",
      "description": "Your neighbours will visit you more often",
      "short_description": "curved 55\" tv"    
    }
  }
}
JSON;
  }

  private function readProductJson(int $id)
  {
    return <<<JSON
{"id":"{$id}","sku":"{$this->randSku}","type":"simple","properties":{"global":{"name":"Mad Hodgin","status":"new","manufacturer":"samsung","is_gluten_free":false,"label_language":["en","it"],"final_gross_price":699},"de":{"name":"Silly Hodgin","status":"new","tax_rate":19},"fr":[],"at":[],"de_de":{"name":"Silly Hodgin","description":"Your neighbours will visit you more often","short_description":"curved 55\" tv"},"fr_fr":[],"at_de":[]}}
JSON;
  }
}

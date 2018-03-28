<?php

class ProductCest
{
  private $randSku;

  function __construct()
  {
    $this->randSku = $this->generateRandomSku();
    echo "\nRandom sku used for the tests: [{$this->randSku}]\n";
  }


  function it_should_fail_creating_product_missing_required_fields(Product_serviceTester $I)
  {
    $payload = $this->createProductJson();
    $payload = str_replace('"name": " Silly Hodgin",', '', $payload);
    $payload = str_replace('"final_gross_price": 5172,', '', $payload);

    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPOST("/product/{$this->randSku}", $payload);
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
    $I->seeResponseContains('"This field is missing. properties[global][name]"');
  }

  function it_should_succeed_creating_product(Product_serviceTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPOST("/product/{$this->randSku}", $this->createProductJson());
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::CREATED);
    $I->seeResponseEquals('');
  }

  function it_should_fail_creating_existing_product(Product_serviceTester $I)
  {
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->sendPOST("/product/{$this->randSku}", $this->createProductJson());
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
    $I->seeResponseEquals('["product_creation_forbidden"]');
  }
//
//  function it_should_succeed_updating_product(Product_serviceTester $I)
//  {
//    $updateJson = <<<JSON
//{
//  "name": "Mad Hodgin ",
//  "final_gross_price": 699,
//  "is_gluten_free": false,
//  "manufacturer": "samsung"
//}
//JSON;
//    $I->haveHttpHeader('Content-Type', 'application/json');
//    $I->sendPUT("/product/{$this->randSku}/de/de_de", $updateJson);
//    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NO_CONTENT);
//    $I->seeResponseEquals('');
//  }
//
//  function it_should_succeed_getting_by_sku(Product_serviceTester $I)
//  {
//    $I->sendGet("/product/{$this->randSku}/de/de_de");
//    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
//    $I->seeResponseIsJson();
//
//    $response = json_decode($I->grabResponse(), true);
//    $expectedResponse = $this->readProductJson($response['id']);
//    $I->seeResponseEquals($expectedResponse);
//  }
//
//  function it_should_fail_getting_non_existing_sku(Product_serviceTester $I)
//  {
//    $I->sendGet('/product/nu3_1/de/de_de');
//    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
//    $I->seeResponseEquals('["product_not_found"]');
//  }
//
//  function it_should_fail_getting_sku_from_wrong_country(Product_serviceTester $I)
//  {
//    $I->sendGet("/product/{$this->randSku}/xx/de_de");
//    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
//    $I->seeResponseEquals('["invalid_country_value"]');
//  }
//
//  function it_should_fail_getting_sku_from_wrong_language(Product_serviceTester $I)
//  {
//    $I->sendGet("/product/{$this->randSku}/de/xx_xx");
//    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
//    $I->seeResponseEquals('["invalid_language_value"]');
//  }

  //this function has to be last
  function clean_up(Product_serviceTester $I)
  {
    /** @var \Nu3\Core\Database\Connection $db */
    $db = $I->getApp()['database.connection'];
    $I->removeProductBySku($db->connectionRes(), $this->randSku);
    $I->removeProductBySku($db->connectionRes(), 'nu3_3');
  }

  private function generateRandomSku() : string
  {
    $maxRand = mt_getrandmax();
    return 'nu3_'. mt_rand($maxRand - 100000, $maxRand);
  }

  private function createProductJson()
  {
    return <<<JSON
{
  "type": "config",
  "properties": {
    "global": {
      "status": "new",
      "name": " Silly Hodgin",
      "type": "Config",
      "final_gross_price": 5172,
      "tax_rate": 19,
      "is_gluten_free": true,
      "is_lactose_free": true,
      "seo_robots": ["noindex", "follow"],
      "seo_title": "Silly Hodgkin",
      "not_supported_attribute": null,
      "manufacturer": "philips2",
      "description": "Your neighbours will visit you more often",
      "short_description": "curved 55\" tv",
      "manufacturer": "philips",
      "label_language": ["en", "it"]
    }
  }
}
JSON;
  }

  private function readProductJson(int $id)
  {
    return <<<JSON
{"id":"{$id}","sku":"{$this->randSku}","type":"Config","name":"Mad Hodgin","status":"new","tax_rate":19,"description":"Your neighbours will visit you more often","manufacturer":"samsung","is_gluten_free":false,"label_language":["en","it"],"final_gross_price":699,"short_description":"curved 55\" tv"}
JSON;
  }
}

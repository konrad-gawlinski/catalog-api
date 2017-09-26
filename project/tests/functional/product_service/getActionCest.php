<?php

class getActionCest
{
  function it_should_succeed(Product_serviceTester $I)
  {
    $I->sendGet('/product/nu3_106/de/de_de');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    $I->seeResponseIsJson();
  }

  function it_should_fail_given_non_existing_sku(Product_serviceTester $I)
  {
    $I->sendGet('/product/nu3_1/de/de_de');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
    $I->seeResponseEquals('["product_not_found"]');
  }

  function it_should_fail_given_wrong_country(Product_serviceTester $I)
  {
    $I->sendGet('/product/nu3_106/xx/de_de');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
    $I->seeResponseEquals('["invalid_country_value"]');
  }

  function it_should_fail_given_wrong_language(Product_serviceTester $I)
  {
    $I->sendGet('/product/nu3_106/de/xx_xx');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
    $I->seeResponseEquals('["invalid_language_value"]');
  }
}

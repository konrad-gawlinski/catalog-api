<?php

class getActionCest
{
  function it_should_return_valid_response(Product_serviceTester $I)
  {
    $I->sendGet('/product/nu3_106/de/de_de');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    $I->seeResponseIsJson();
  }

  function it_should_return_invalid_response_given_non_existing_sku(Product_serviceTester $I)
  {
    $I->sendGet('/product/nu3_1/de/de_de');
    $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
    $I->seeResponseEquals('["product_not_found"]');
  }

}

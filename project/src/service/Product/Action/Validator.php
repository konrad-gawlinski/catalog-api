<?php

namespace Nu3\Service\Product\Action;

use Nu3\Config;
use Nu3\Feature\Config as AppConfig;
use Nu3\Service\Product\Request;
use Nu3\Core\Violation;
use Nu3\Service\Product\ErrorKey;

class Validator
{
  use AppConfig;

  /**
   * @param Request $request
   *
   * @return Violation[] array
   */
  function validateRequest($request) : array
  {
    $violations = [];

    $violations += $this->validateRequiredSku($request->getSku());
    $violations += $this->validateCountry($request->getCountry());
    $violations += $this->validateLanguage($request->getLanguage());

    return $violations;
  }

  /**
   * @return Violation[]
   */
  private function validateRequiredSku(string $sku) : array
  {
    if (empty($sku)) {
      return [new Violation(ErrorKey::SKU_IS_REQUIRED)];
    }

    return [];
  }

  /**
   * @return Violation[]
   */
  private function validateCountry(string $country) : array
  {
    $availableCountries = $this->config()[Config::COUNTRY][Config::AVAILABLE];

    if (!in_array($country, $availableCountries)) {
      return [new Violation(ErrorKey::INVALID_COUNTRY_VALUE)];
    }

    return [];
  }

  /**
   * @return Violation[]
   */
  private function validateLanguage(string $language) : array
  {
    $availableCountries = $this->config()[Config::LANGUAGE][Config::AVAILABLE];

    if (!in_array($language, $availableCountries)) {
      return [new Violation(ErrorKey::INVALID_LANGUAGE_VALUE)];
    }

    return [];
  }
}

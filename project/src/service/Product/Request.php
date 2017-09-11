<?php

namespace Nu3\Service\Product;

class Request
{
  const PROPERTY_SKU = 'sku';
  const PROPERTY_COUNTRY = 'country';
  const PROPERTY_LANGUAGE = 'lang';

  protected $params = [];

  function __construct(array $params)
  {
    $this->params = $params;
  }

  function getSku(): string
  {
    return $this->getValue(self::PROPERTY_SKU);
  }

  function getCountry(): string
  {
    return $this->getValue(self::PROPERTY_COUNTRY);
  }

  function getLanguage(): string
  {
    return $this->getValue(self::PROPERTY_LANGUAGE);
  }

  /**
   * @param string $name
   * @param mixed $default
   *
   * @return mixed
   */
  protected function getValue(string $name, $default='')
  {
    if (isset($this->params[$name])) return $this->params[$name];

    return $default;
  }
}

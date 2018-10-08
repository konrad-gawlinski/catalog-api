<?php

namespace Nu3\Service\Product;

class Request
{
  const PROPERTY_ID = 'id';
  const PROPERTY_PAYLOAD = 'payload';

  protected $params = [];

  function __construct(array $params)
  {
    $this->params = $params;
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

<?php
namespace Nu3\Feature;

trait Config
{
  private $config = [];

  function setConfig(array $config)
  {
    $this->config = $config;
  }

  protected function config() : array
  {
    return $this->config;
  }
}
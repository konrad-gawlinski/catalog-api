<?php
namespace Nu3\Feature;

use Silex\Application;

trait App
{
  private $app = [];

  function setApp(Application $app)
  {
    $this->app = $app;
  }

  protected function app() : Application
  {
    return $this->app;
  }
}
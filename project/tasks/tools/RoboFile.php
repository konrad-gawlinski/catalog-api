<?php

define('APPLICATION_ROOT', __DIR__ . '/../..');

require_once APPLICATION_ROOT . '/vendor/autoload.php';

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
  function runBuildConfig()
  {
    $this->taskExec('php run.php')
        ->dir('../../tools/config_builder/')
        ->run();
  }
  function runBuildProductValidations()
  {
    $this->taskExec('php run.php')
      ->dir('../../tools/validation_builder/')
      ->run();
  }
}

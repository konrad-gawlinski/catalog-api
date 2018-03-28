<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
  function runUnitTests()
  {
    $this->taskExec('./phpspec')
      ->dir('../../tests/unit/')
      ->arg('run')
      ->args('--no-interaction')
      ->run();
  }

  function runIntegrationTests()
  {
    $this->taskExec('./codecept')
      ->dir('../../tests/integration/')
      ->arg('run')
      ->run();
  }

  function runFunctionalTests()
  {
    $this->taskExec('./codecept')
      ->dir('../../tests/functional/')
      ->arg('run')
      ->run();
  }
}

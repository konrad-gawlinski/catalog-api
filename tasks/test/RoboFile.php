<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
  function runUnitTests($opts = [])
  {
    $task = $this->taskExec('./phpspec')->dir('../../tests/unit/')->arg('run')->option('no-interaction');
    if ($opts['verbose']) $task->option('verbose');

    $task->run();
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

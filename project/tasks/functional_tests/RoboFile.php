<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
  private const TESTS_DIR = '../../tests/functional/';

  function run()
  {
    $this->taskExec('./codecept')
      ->dir(self::TESTS_DIR)
      ->arg('run')
      ->run();
  }
}

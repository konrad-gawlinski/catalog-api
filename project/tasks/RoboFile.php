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
    $this->_exec('./robo --load-from ./unit_tests run');
  }

  function runFunctionalTests()
  {
    $this->_exec('./robo --load-from ./functional_tests run');
  }
}

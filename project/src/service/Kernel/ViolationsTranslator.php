<?php

namespace Nu3\Service\Kernel;

use Nu3\Core\Violation;

trait ViolationsTranslator
{
  /**
   * @param Violation[] $violations
   *
   * @return string
   */
  private function violationsToJson(array $violations) : string
  {
    $result = [];
    /** @var Violation $violation */
    foreach ($violations as $violation) {
      $result[] = $violation->message();
    }

    return json_encode(($result));
  }
}
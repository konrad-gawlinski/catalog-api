<?php

namespace Nu3\Core\Database\Gateway;

use Nu3\Core\Database\Connection;

class Base
{
  /**
   * @var Connection
   */
  protected $dbconn;

  function __construct(Connection $dbconn)
  {
    $this->dbconn = $dbconn;
  }
}

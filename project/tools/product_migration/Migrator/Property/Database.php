<?php

namespace Nu3\ProductMigration\Migrator\Property;

trait Database
{
  private $dbCon;

  function setDbCon($dbCon)
  {
    $this->dbCon = $dbCon;
  }
}
<?php
namespace Nu3\Feature;

use Nu3\Core\RegionCheck as RegionCheckObject;

trait RegionCheck
{
  private $regionCheck;

  function setRegionCheck(RegionCheckObject $regionCheck)
  {
    $this->regionCheck = $regionCheck;
  }

  protected function regionCheck() : RegionCheckObject
  {
    return $this->regionCheck;
  }
}
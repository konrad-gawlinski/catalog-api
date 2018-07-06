<?php
namespace Nu3\Feature;

use Nu3\Core\RegionUtils as RegionUtilsObject;

trait RegionUtils
{
  private $regionUtils;

  function setRegionUtils(RegionUtilsObject $regionCheck)
  {
    $this->regionUtils = $regionCheck;
  }

  protected function regionUtils() : RegionUtilsObject
  {
    return $this->regionUtils;
  }
}

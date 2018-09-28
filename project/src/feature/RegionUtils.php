<?php
namespace Nu3\Feature;

use Nu3\Core\RegionUtils as RegionUtilsInstance;

trait RegionUtils
{

  /** @var RegionUtilsInstance */
  private $regionUtils;

  function setRegionUtils(RegionUtilsInstance $regionCheck)
  {
    $this->regionUtils = $regionCheck;
  }

  protected function regionUtils() : RegionUtilsInstance
  {
    return $this->regionUtils;
  }
}

<?php

namespace Nu3\Core;

class RegionUtils
{
  function isGlobal(string $region) : bool
  {
    return $region === 'global';
  }

  /**
   * The check is as simple as possible so that it's quick
   * The check is based on ISO Alpha-2, it's always only 2 letter code
   */
  function isCountry(string $region) : bool
  {
    return strlen($region) === 2;
  }

  /**
   * The check is as simple as possible so that it's quick
   * The check is based on a code concatenation xx_yy where,
   * xx is ISO Alpha-2
   * yy is ISO 639-1
   * It's always 5 letter code
   */
  function isLanguage(string $region) : bool
  {
    return strlen($region) === 5;
  }

  function intersectValidRegionCombinations(array $regions, array $regionCombinations)
  {
    $result = [];
    foreach ($regionCombinations as $combination) {
      foreach ($regions as $region) {
        list($country, $language) = $combination;
        $uniqueKey = $country . '-' . $language;
        if ($country === $region || $language === $region) {
          $result[$uniqueKey] = $combination;
        }
      }
    }

    return array_values($result);
  }
}

<?php

namespace spec\Core\Nu3\Core;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RegionUtilsSpec extends ObjectBehavior
{
  function it_should_be_global_region()
  {
    $this->isGlobal('global')->shouldBe(true);
  }

  function it_should_not_be_global_region()
  {
    $this->isGlobal('fake')->shouldBe(false);
    $this->isGlobal('de')->shouldBe(false);
    $this->isGlobal('de_de')->shouldBe(false);
  }

  function it_should_be_country_region()
  {
    $this->isCountry('DE')->shouldBe(true);
    $this->isCountry('at')->shouldBe(true);
    $this->isCountry('Fr')->shouldBe(true);
  }

  function it_should_not_be_country_region()
  {
    $this->isCountry('fake')->shouldBe(false);
    $this->isCountry('global')->shouldBe(false);
    $this->isCountry('ENG')->shouldBe(false);
    $this->isCountry('B')->shouldBe(false);
    $this->isCountry('de_de')->shouldBe(false);
  }

  function it_should_be_language_region()
  {
    $this->isLanguage('de_de')->shouldBe(true);
    $this->isLanguage('FR_fr')->shouldBe(true);
    $this->isLanguage('at_DE')->shouldBe(true);
  }

  function it_should_not_be_language_region()
  {
    $this->isLanguage('global')->shouldBe(false);
    $this->isLanguage('fake')->shouldBe(false);
    $this->isLanguage('de')->shouldBe(false);
    $this->isLanguage('FR')->shouldBe(false);
  }

  function it_should_find_all_possible_region_combinations()
  {
    $touchedRegions = [
      'de',
      'at_de',
      'de_de'
    ];

    $regionCombinations = [
      ['de', 'de_de'],
      ['at', 'de_de'],
      ['dk', 'dk_dk'],
      ['at', 'at_de'],
      ['fr', 'fr_fr']
    ];

    $this->intersectValidRegionCombinations($touchedRegions, $regionCombinations)->shouldReturn([
      ['de', 'de_de'],
      ['at', 'de_de'],
      ['at', 'at_de'],
    ]);
  }
}

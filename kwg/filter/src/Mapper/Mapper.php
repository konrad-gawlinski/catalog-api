<?php

namespace Kwg\Filter\Mapper;

use Kwg\Filter\MetaData;

interface Mapper
{
  const PROPERTY_PROPERTIES = 'properties';

  function normalize() : MetaData;
}
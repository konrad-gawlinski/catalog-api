<?php

namespace Nu3\Service\Product\Action;

interface RequestValidator
{
  function validate($request) : array;
}

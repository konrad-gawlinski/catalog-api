<?php

namespace Nu3\Service\Product\Action;

interface Validator
{
  function validateRequest($request) : array;
}
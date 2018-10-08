<?php

namespace Nu3\Service\Product\Validator;

interface ValidatableProduct
{
  function validate(int $productId, array $regionPairs) : array;
}

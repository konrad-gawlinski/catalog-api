<?php

namespace Nu3\Service\Product\Action\UpdateProduct\Validator;

interface ValidatableProduct
{
  function validate(int $productId, array $regionPairs) : array;
}

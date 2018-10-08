<?php

namespace Nu3\Service\Product\Entity;

interface ProductStatus
{
  const NEW = 'new';
  const APPROVED = 'approved';
  const NOT_LISTED = 'not listed';
  const UNAVAILABLE = 'unavailable';
}
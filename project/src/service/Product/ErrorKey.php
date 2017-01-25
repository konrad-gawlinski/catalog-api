<?php

namespace Nu3\Service\Product;

interface ErrorKey
{
  const INVALID_PRODUCT_PAYLOAD_VALIDATION_FILE_PATH = 'invalid_product_payload_validation_file_path';
  const INVALID_PRODUCT_VALIDATION_RULES_FILE_PATH = 'invalid_product_validation_rules_file_path';
  const INVALID_PRODUCT_DEFAULT_VALUES = 'invalid_product_default_values';
  const NEW_PRODUCT_REQUIRES_TYPE = 'new_product_requires_type';
}
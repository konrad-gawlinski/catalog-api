<?php

namespace Nu3\Service\Product;

interface ErrorKey
{
  const NEW_PRODUCT_REQUIRES_TYPE = 'new_product_requires_type';
  const INVALID_PRODUCT_TYPE = 'invalid_product_type';

  const SKU_IS_REQUIRED = 'sku_is_required';
  const PRODUCT_CREATION_RESTRICTED = 'product_creation_restricted';
  const PRODUCT_UPDATE_RESTRICTED = 'product_update_restricted';
  const INVALID_COUNTRY_VALUE = 'invalid_country_value';
  const INVALID_LANGUAGE_VALUE = 'invalid_language_value';
  const PRODUCT_NOT_FOUND = 'product_not_found';

  const PRODUCT_SAVE_STORAGE_ERROR = 'product_save_storage_error';
  const PRODUCT_VALIDATION_ERROR = 'product_validation_error';
}

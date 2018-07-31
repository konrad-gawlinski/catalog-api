<?php

namespace Nu3\Service\Product;

interface ErrorKey
{
  const NEW_PRODUCT_REQUIRES_TYPE = 'new_product_requires_type';
  const INVALID_PRODUCT_TYPE = 'invalid_product_type';

  const ID_IS_REQUIRED = 'id_is_required';
  const ID_HAS_TO_BE_A_NUMBER = 'id_has_to_be_a_number';
  const SKU_IS_REQUIRED = 'sku_is_required';
  const PRODUCT_UPDATE_FORBIDDEN = 'product_update_forbidden';
  const PRODUCT_ALREADY_CREATED = 'product_already_created';
  const INVALID_COUNTRY_VALUE = 'invalid_country_value';
  const INVALID_LANGUAGE_VALUE = 'invalid_language_value';
  const PRODUCT_NOT_FOUND = 'product_not_found';
  const EMPTY_PRODUCT_PROPERTIES = 'empty_product_properties';

  const PRODUCT_SAVE_STORAGE_ERROR = 'product_save_storage_error';
  const PRODUCT_VALIDATION_ERROR = 'product_validation_error';
}

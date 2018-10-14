<?php

namespace Nu3;

interface Config
{
  const DB = 'database';
  const DB_DSN = 'dsn';
  const DB_DATA_SCHEMA = 'data_schema';
  const DB_PROCEDURES_SCHEMA = 'procedures_schema';

  const REGION = 'region';
  const GLOBAL_REGION = 'global';
  const COUNTRY_REGION = 'country';
  const LANGUAGE_REGION = 'language';

  const PRODUCT = 'product';
  const PRODUCT_TYPE_CONFIG = 'config';
  const PRODUCT_TYPE_SIMPLE = 'simple';
  const PRODUCT_TYPE_BUNDLE = 'bundle';
  const DEFAULT_VALUES = 'default_values';
  const VALIDATION_RULES = 'validation_rules';

  const SHOP = 'shop';
  const REGION_PAIRS = 'region_pairs';
}

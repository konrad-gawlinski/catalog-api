<?php
return array (
  'database' => 
  array (
    'username' => 'postgres',
    'password' => 'postgres',
    'host' => '172.18.0.3',
    'database' => 'catalog_api',
    'data_schema' => 'catalog',
    'procedures_schema' => 'catalog_sp',
  ),
  'country' => 
  array (
    'available' => 
    array (
      0 => 'de',
    ),
  ),
  'language' => 
  array (
    'available' => 
    array (
      0 => 'de_de',
    ),
  ),
  'product' => 
  array (
    'Config' => 
    array (
      'validation_rules' => 'config.yml',
    ),
    'Bundle' => 
    array (
      'validation_rules' => 'config.yml',
    ),
  ),
);

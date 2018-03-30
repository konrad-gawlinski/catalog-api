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
    'simple' => 
    array (
      'validation_rules' => 'simple.yml',
    ),
    'config' => 
    array (
      'validation_rules' => 'config.yml',
    ),
    'bundle' => 
    array (
      'validation_rules' => 'bundle.yml',
    ),
  ),
);

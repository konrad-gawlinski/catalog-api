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
  'region' => 
  array (
    'global' => 
    array (
      0 => 'global',
    ),
    'country' => 
    array (
      0 => 'de',
      1 => 'fr',
      2 => 'at',
    ),
    'language' => 
    array (
      0 => 'de_de',
      1 => 'fr_fr',
      2 => 'at_de',
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
  'shop' => 
  array (
    'region_combinations' => 
    array (
      0 => 
      array (
        0 => 'de',
        1 => 'de_de',
      ),
      1 => 
      array (
        0 => 'fr',
        1 => 'fr_fr',
      ),
      2 => 
      array (
        0 => 'at',
        1 => 'at_de',
      ),
      3 => 
      array (
        0 => 'at',
        1 => 'de_de',
      ),
    ),
  ),
);

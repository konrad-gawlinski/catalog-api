<?php
$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$serviceContainer->checkVersion('2.0.0-dev');
$serviceContainer->setAdapterClass('nu3_catalog', 'pgsql');
$manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
$manager->setConfiguration(array (
  'dsn' => 'pgsql:host=172.17.0.1;port=5432;dbname=nu3_catalog;',
  'user' => 'postgres',
  'password' => 'postgres',
  'classname' => 'Propel\\Runtime\\Connection\\ConnectionWrapper',
  'settings' =>
  array (
    'charset' => 'utf8',
    'queries' =>
    array (
      'utf8' => 'SET NAMES \'UTF8\'',
    ),
  ),
  'model_paths' =>
  array (
    0 => 'src',
    1 => 'vendor',
  ),
));
$manager->setName('nu3_catalog');
$serviceContainer->setConnectionManager('nu3_catalog', $manager);
$serviceContainer->setDefaultDatasource('nu3_catalog');
<?php

define('APPLICATION_ROOT', __DIR__ . '/../../');

/** @var \Composer\Autoload\ClassLoader() $loader */
$loader = require_once APPLICATION_ROOT . 'vendor/autoload.php';

$sourceDir = APPLICATION_ROOT . 'src/service/Product/config/default_values/';
$targetDir = APPLICATION_ROOT . 'src/service/Product/SaveAction/default_values/';
$config = require(__DIR__ . '/configuration.php');

foreach ($config as $target => $sources) {
  $result = [];
  foreach ($sources as $source) {
    $json = file_get_contents($sourceDir . $source . '.json');
    $result = array_replace_recursive(json_decode($json, true), $result);
  }

  $phpCode = var_export($result, true);
  file_put_contents($targetDir . $target . '.php', "<?php\nreturn {$phpCode};\n");
}

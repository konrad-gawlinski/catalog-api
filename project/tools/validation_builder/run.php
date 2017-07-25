<?php
use Symfony\Component\Yaml\Yaml;

define('APPLICATION_ROOT', __DIR__ . '/../../');

/** @var \Composer\Autoload\ClassLoader() $loader */
$loader = require_once APPLICATION_ROOT . 'vendor/autoload.php';

$sourceDir = APPLICATION_ROOT . 'src/service/Product/config/validation_rules/';
$targetDir = APPLICATION_ROOT . 'src/service/Product/validation_rules/';
$config = require(__DIR__ . '/configuration.php');

foreach ($config as $target => $sources) {
  $result = [];
  foreach ($sources as $source) {
    $array = Yaml::parse(file_get_contents($sourceDir . $source . '.yml'));
    $result = array_replace_recursive($array, $result);
  }

  $yml = Yaml::dump($result, 50, 2);
  file_put_contents($targetDir . $target . '.yml', $yml);
}

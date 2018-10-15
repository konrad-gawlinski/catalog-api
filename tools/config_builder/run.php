<?php

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Dotenv\Dotenv;

define('APPLICATION_ROOT', __DIR__.'/../../');
define('APP_ENV', getenv('APP_ENV'));

if (empty(APP_ENV))
  die('Specify APP_ENV environment variable');

/** @var \Composer\Autoload\ClassLoader() $loader */
$loader = require_once APPLICATION_ROOT .'vendor/autoload.php';
$loader->addPsr4('Nu3\\Configuration\\', APPLICATION_ROOT.'tools/config_builder');
(new Dotenv())->load('/etc/environment');

$defaultConfig = Yaml::parse(file_get_contents(APPLICATION_ROOT .'config/properties/default.yml'));
$environmentConfig = Yaml::parse(file_get_contents(APPLICATION_ROOT .'config/properties/'. APP_ENV .'.yml'));
$localConfig = fill_placeholders(file_get_contents(APPLICATION_ROOT .'config/local/config.yml'));
$localConfig = Yaml::parse($localConfig);

$config = array_union_recursive(array_union_recursive($defaultConfig, $environmentConfig), $localConfig);
$configSourceCode = var_export($config, true);
file_put_contents(APPLICATION_ROOT.'config/config.php', "<?php\nreturn {$configSourceCode};\n");

function array_union_recursive($array1, $array2) : array
{
  foreach ($array2 as $key => $value) {
    if (is_array($value)) $array1[$key] = array_union_recursive($array1[$key], $array2[$key]);
    else $array1[$key] = $value;
  }

  return $array1;
}

function fill_placeholders(string $input) : string
{
  preg_match_all("/(%[^%]+%)/", $input, $result);
  $placeholders = $result[1];

  $replacements = array_map(function($placeholder) {
    return getenv(trim($placeholder, '%'));
  }, $placeholders);

  return str_replace($placeholders, $replacements, $input);
}

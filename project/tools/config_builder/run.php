<?php

use Symfony\Component\Yaml\Yaml;

define('APPLICATION_ROOT', __DIR__.'/../../');

/** @var \Composer\Autoload\ClassLoader() $loader */
$loader = require_once APPLICATION_ROOT.'vendor/autoload.php';
$loader->addPsr4('Nu3\\Configuration\\', APPLICATION_ROOT.'tools/config_builder');

$configDefault = Yaml::parse(file_get_contents(APPLICATION_ROOT.'config/default.yml'));
$configLocal = Yaml::parse(file_get_contents(APPLICATION_ROOT.'config/local.yml'));

$configProcessor = new \Nu3\Configuration\Processor();
$config = var_export($configProcessor->run([$configDefault, $configLocal]), true);
file_put_contents(APPLICATION_ROOT.'config/config.php', "<?php\nreturn {$config};\n");

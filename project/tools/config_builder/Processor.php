<?php

namespace Nu3\Configuration;

use Symfony\Component\Config\Definition\Processor as SymfonyProcessor;

class Processor
{
  function run(array $configs) : array
  {
    $processor = new SymfonyProcessor();
    return $processor->processConfiguration(new Configuration(), $configs);
  }
}
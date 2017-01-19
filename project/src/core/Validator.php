<?php

namespace Nu3\Core;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
  function buildValidator(string $ymlConfigPath) : ValidatorInterface
  {
    return \Symfony\Component\Validator\Validation::createValidatorBuilder()
      ->addYamlMapping($ymlConfigPath)
      ->setMetadataCache(
        new \Symfony\Component\Validator\Mapping\Cache\DoctrineCache(
          new \Doctrine\Common\Cache\ArrayCache()
        ))
      ->getValidator();
  }
}
<?php

namespace Nu3\Core;

use Symfony\Component\Validator\ValidatorBuilderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Validator
{
  /** @var ValidatorInterface */
  private $validator;

  function __construct(ValidatorBuilderInterface $validatorBuilder)
  {
    $this->validator = $this->buildValidator($validatorBuilder);
  }

  function validate(Payload $entity) : ConstraintViolationListInterface
  {
    return $this->validator->validate($entity);
  }

  private function buildValidator(ValidatorBuilderInterface $validatorBuilder) : ValidatorInterface
  {
    return $validatorBuilder->getValidator();
  }
}
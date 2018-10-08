<?php

namespace spec\Product\Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Service\Product\Action\UpdateProduct\Factory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProductValidatorSpec extends ObjectBehavior
{
    /** @var Factory */
    private static $factoryDouble;

    function let(Factory $factory, $entityValidator, $entityBuilder, $valueFilter, $productGateway)
    {
        if (!self::$factoryDouble) {
            $entityValidator->beADoubleOf('Nu3\Service\Product\Entity\Validator');
            $entityBuilder->beADoubleOf('Nu3\Service\Product\EntityBuilder');
            $valueFilter->beADoubleOf('Nu3\Service\Product\ValueFilter');
            $productGateway->beADoubleOf('Nu3\Core\Database\QueryRunner\Product');

            $factory->createEntityValidator()->willReturn($entityValidator);
            $factory->createEntityBuilder()->willReturn($entityBuilder);
            $factory->createValueFilter()->willReturn($valueFilter);
            $factory->createProductGateway()->willReturn($productGateway);

            self::$factoryDouble = $factory;
        }

        $this->beConstructedWith($factory);
    }
}

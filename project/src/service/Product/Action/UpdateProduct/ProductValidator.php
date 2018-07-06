<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Service\Product\Entity;
use Nu3\Core\Violation;
use Nu3\Service\Product\TransferObject;
use Nu3\Service\Product\EntityBuilder;
use Nu3\Service\Product\ValueFilter;
use Nu3\Feature\Config as ConfigFeature;

class ProductValidator
{
    use ConfigFeature;

    /** @var EntityBuilder */
    private $entityBuilder;

    /** @var Entity\Validator */
    private $entityValidator;

    /** @var ValueFilter */
    private $valueFilter;

    /** @var Factory */
    protected $factory;

    function __construct(Factory $factory)
    {
        $this->factory = $factory;

        $this->entityValidator = $factory->createEntityValidator();
        $this->entityBuilder = $factory->createEntityBuilder();
        $this->valueFilter = $factory->createValueFilter();
    }

    /**
     * @return Violation[]
     */
    function validate(array $storedProductProperties, TransferObject $dto) : array
    {
        $product = $this->mergeStoredPropertiesWithRequestedProperties($storedProductProperties, $dto);
        $violations = $this->factory->createEntityValidator()->validate($product);
        if ($violations) return $violations;

        return [];
    }

    private function mergeStoredPropertiesWithRequestedProperties(array $storedProductProperties, TransferObject $dto) : Entity\Product
    {
        $productEntity = $this->factory->createProductEntity();
        $this->entityBuilder->fillEntityFromDbArray($productEntity, $storedProductProperties);
        $this->entityBuilder->applyDtoAttributesToEntity($dto, $productEntity);
        $this->valueFilter->filterEntity($productEntity);

        return $productEntity;
    }
}

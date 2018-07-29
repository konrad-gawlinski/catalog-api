<?php

namespace Nu3\Service\Product\Action\UpdateProduct;

use Nu3\Service\Product\Entity;
use Nu3\Core\Violation;
use Nu3\Service\Product\TransferObject;
use Nu3\Service\Product\EntityBuilder;
use Nu3\Service\Product\ValueFilter;
use Nu3\Core\Database\Gateway\Product as ProductGateway;
use Nu3\Config;
use Nu3\Feature\Config as ConfigFeature;
use Nu3\Feature\RegionUtils;

class ProductValidator
{
    use ConfigFeature;
    use RegionUtils;

    /** @var EntityBuilder */
    private $entityBuilder;

    /** @var Entity\Validator */
    private $entityValidator;

    /** @var ValueFilter */
    private $valueFilter;

    /** @var ProductGateway */
    protected $productGateway;

    /** @var Factory */
    protected $factory;

    function __construct(Factory $factory)
    {
        $this->factory = $factory;

        $this->entityValidator = $factory->createEntityValidator();
        $this->entityBuilder = $factory->createEntityBuilder();
        $this->valueFilter = $factory->createValueFilter();
        $this->productGateway = $factory->createProductGateway();
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

    function setProductGateway(ProductGateway $productGateway)
    {
        $this->productGateway = $productGateway;
    }
}

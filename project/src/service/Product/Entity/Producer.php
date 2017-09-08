<?php

namespace Nu3\Service\Product\Entity;

use Nu3\Service\Product\TransferObject;

class Producer
{
  function applyDtoAttributesToEntity(TransferObject $dto, Product $entity)
  {
    $entity->sku = $dto->getSku();

    $this->applyAttributes($dto, $entity);
  }

  private function applyAttributes(TransferObject $dto, Product $entity)
  {
    $properties = $dto->getProductProperties();
    $attributesMap = $this->getDto2DbPropertyMap();

    foreach ($properties as $property => $value) {
      if (isset($attributesMap[$property])) {
        $entity->properties[$attributesMap[$property]] = $value;
      }
    }
  }

  private function getDto2DbPropertyMap() : array
  {
    return [
      'status'=> 'status',
      'product_family' => 'product_family',
      'name'=> 'name',
      'type'=> 'type',
      'final_price'=> 'final_price',
      'tax_rate'=> 'tax_rate',
      'is_gluten_free'=> 'is_gluten_free',
      'is_lactose_free'=> 'is_lactose_free',
      'seo_robots'=> 'seo_robots',
      'seo_title'=> 'se_title',
      'manufacturer'=> 'manufacturer',
      'description'=> 'description',
      'short_description'=> 'short_description',
      'label_language'=> 'label_language'
    ];
  }
}

<?php

namespace App\Mapper\Country;

use App\Dto\Country\Out\CountryOut;
use App\Entity\Country\Country;
use App\Mapper\Country\Interface\ICountryMapper;

class CountryMapper implements ICountryMapper
{
    public function toOut(Country $entity): CountryOut
    {
        $dto = new CountryOut();
        $dto->setId($entity->getId())
            ->setUuid($entity->getUuid())
            ->setIsoCode($entity->getIsoCode())
            ->setName($entity->getName())
            ->setIsDefault($entity->isDefault())
            ->setCreatedAt($entity->getCreatedAt()?->format('Y-m-d H:i:s'))
            ->setUpdatedAt($entity->getUpdatedAt()?->format('Y-m-d H:i:s'));
            
        return $dto;
    }
    
    public function toArray(CountryOut $dto): array
    {
        return [
            'id' => $dto->getId(),
            'uuid' => $dto->getUuid(),
            'iso_code' => $dto->getIsoCode(),
            'name' => $dto->getName(),
            'is_default' => $dto->isDefault(),
            'created_at' => $dto->getCreatedAt(),
            'updated_at' => $dto->getUpdatedAt()
        ];
    }
}

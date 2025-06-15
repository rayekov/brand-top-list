<?php

namespace App\Mapper\TopList;

use App\Dto\TopList\Out\TopListEntryOut;
use App\Entity\TopList\TopListEntry;
use App\Mapper\Brand\Interface\IBrandMapper;
use App\Mapper\Country\Interface\ICountryMapper;
use App\Mapper\TopList\Interface\ITopListMapper;

class TopListMapper implements ITopListMapper
{
    private IBrandMapper $brandMapper;
    private ICountryMapper $countryMapper;

    public function __construct(IBrandMapper $brandMapper, ICountryMapper $countryMapper)
    {
        $this->brandMapper = $brandMapper;
        $this->countryMapper = $countryMapper;
    }

    public function toOut(TopListEntry $entity): TopListEntryOut
    {
        $dto = new TopListEntryOut();
        $dto->setId($entity->getId())
            ->setUuid($entity->getUuid())
            ->setPosition($entity->getPosition())
            ->setIsActive($entity->isActive())
            ->setBrand($this->brandMapper->toOut($entity->getBrand()))
            ->setCountry($this->countryMapper->toOut($entity->getCountry()))
            ->setCreatedAt($entity->getCreatedAt()?->format('Y-m-d H:i:s'))
            ->setUpdatedAt($entity->getUpdatedAt()?->format('Y-m-d H:i:s'));
            
        return $dto;
    }
    
    public function toArray(TopListEntryOut $dto): array
    {
        return [
            'id' => $dto->getId(),
            'uuid' => $dto->getUuid(),
            'position' => $dto->getPosition(),
            'is_active' => $dto->isActive(),
            'brand' => $this->brandMapper->toArray($dto->getBrand()),
            'country' => $this->countryMapper->toArray($dto->getCountry()),
            'created_at' => $dto->getCreatedAt(),
            'updated_at' => $dto->getUpdatedAt()
        ];
    }
}

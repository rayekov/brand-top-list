<?php

namespace App\Mapper\Brand;

use App\Dto\Brand\In\BrandCreateIn;
use App\Dto\Brand\Out\BrandOut;
use App\Entity\Brand\Brand;
use App\Mapper\Brand\Interface\IBrandMapper;
use Symfony\Component\HttpFoundation\Request;

class BrandMapper implements IBrandMapper
{
    public function fromCreateRequest(Request $request): BrandCreateIn
    {
        $data = json_decode($request->getContent(), true) ?? [];
        
        $dto = new BrandCreateIn();
        $dto->setBrandName($data['brand_name'] ?? null)
            ->setBrandImage($data['brand_image'] ?? null)
            ->setRating(isset($data['rating']) ? (int)$data['rating'] : null);
            
        return $dto;
    }
    
    public function toEntity(BrandCreateIn $dto): Brand
    {
        $entity = new Brand();
        $entity->setBrandName($dto->getBrandName())
               ->setBrandImage($dto->getBrandImage())
               ->setRating($dto->getRating());
               
        return $entity;
    }
    
    public function toOut(Brand $entity): BrandOut
    {
        $dto = new BrandOut();
        $dto->setBrandId($entity->getId())
            ->setUuid($entity->getUuid())
            ->setBrandName($entity->getBrandName())
            ->setBrandImage($entity->getBrandImage())
            ->setRating($entity->getRating())
            ->setCreatedAt($entity->getCreatedAt()?->format('Y-m-d H:i:s'))
            ->setUpdatedAt($entity->getUpdatedAt()?->format('Y-m-d H:i:s'));
            
        return $dto;
    }
    
    public function toArray(BrandOut $dto): array
    {
        return [
            'brand_id' => $dto->getBrandId(),
            'uuid' => $dto->getUuid(),
            'brand_name' => $dto->getBrandName(),
            'brand_image' => $dto->getBrandImage(),
            'rating' => $dto->getRating(),
            'created_at' => $dto->getCreatedAt(),
            'updated_at' => $dto->getUpdatedAt()
        ];
    }
}

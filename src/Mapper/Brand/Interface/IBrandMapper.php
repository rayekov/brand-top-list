<?php

namespace App\Mapper\Brand\Interface;

use App\Dto\Brand\In\BrandCreateIn;
use App\Dto\Brand\Out\BrandOut;
use App\Entity\Brand\Brand;
use App\Mapper\IBaseMapper;
use Symfony\Component\HttpFoundation\Request;

interface IBrandMapper extends IBaseMapper
{
    public function fromCreateRequest(Request $request): BrandCreateIn;
    
    public function toEntity(BrandCreateIn $dto): Brand;
    
    public function toOut(Brand $entity): BrandOut;
    
    public function toArray(BrandOut $dto): array;
}

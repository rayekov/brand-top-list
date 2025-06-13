<?php

namespace App\Service\Brand\Implementation;

use App\Constant\GenericResponseStatuses;
use App\Dto\GenericResponse;
use App\Entity\Brand\Brand;
use App\Repository\Brand\BrandRepository;
use App\Service\Brand\Interface\IBrandService;
use App\Service\Traits\BaseServiceTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BrandService implements IBrandService
{
    use BaseServiceTrait;

    private BrandRepository $repository;

    public function __construct(BrandRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createBrand(Request $request): GenericResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data || !isset($data['brand_name']) || !isset($data['brand_image']) || !isset($data['rating'])) {
            return $this->result(
                null, 
                GenericResponseStatuses::VALIDATION_ERROR, 
                Response::HTTP_BAD_REQUEST,
                ['Missing required fields: brand_name, brand_image, rating']
            );
        }

        $brand = new Brand();
        $brand->setBrandName($data['brand_name'])
              ->setBrandImage($data['brand_image'])
              ->setRating((int)$data['rating']);

        $this->persist($brand);

        return $this->result($this->toBrandArray($brand));
    }

    public function findAll(): GenericResponse
    {
        $brands = $this->repository->findAllOrderedByRating();
        $result = array_map([$this, 'toBrandArray'], $brands);
        
        return $this->result($result);
    }

    public function findOne(string $uuid): GenericResponse
    {
        $brand = $this->repository->getByUuid($uuid);
        
        if (!$brand) {
            return $this->result(
                null, 
                GenericResponseStatuses::NOT_FOUND, 
                Response::HTTP_NOT_FOUND,
                ['Brand not found']
            );
        }

        return $this->result($this->toBrandArray($brand));
    }

    public function updateBrand(string $uuid, Request $request): GenericResponse
    {
        $brand = $this->repository->getByUuid($uuid);
        
        if (!$brand) {
            return $this->result(
                null, 
                GenericResponseStatuses::NOT_FOUND, 
                Response::HTTP_NOT_FOUND,
                ['Brand not found']
            );
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['brand_name'])) {
            $brand->setBrandName($data['brand_name']);
        }
        if (isset($data['brand_image'])) {
            $brand->setBrandImage($data['brand_image']);
        }
        if (isset($data['rating'])) {
            $brand->setRating((int)$data['rating']);
        }

        $this->persist($brand);

        return $this->result($this->toBrandArray($brand));
    }

    public function deleteBrand(string $uuid): GenericResponse
    {
        $brand = $this->repository->getByUuid($uuid);
        
        if (!$brand) {
            return $this->result(
                null, 
                GenericResponseStatuses::NOT_FOUND, 
                Response::HTTP_NOT_FOUND,
                ['Brand not found']
            );
        }

        $this->delete($brand);

        return $this->result(['message' => 'Brand deleted successfully']);
    }

    private function toBrandArray(Brand $brand): array
    {
        return [
            'brand_id' => $brand->getId(),
            'uuid' => $brand->getUuid(),
            'brand_name' => $brand->getBrandName(),
            'brand_image' => $brand->getBrandImage(),
            'rating' => $brand->getRating(),
            'created_at' => $brand->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $brand->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }
}

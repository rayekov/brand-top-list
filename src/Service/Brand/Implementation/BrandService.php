<?php

namespace App\Service\Brand\Implementation;

use App\Constant\GenericResponseStatuses;
use App\Dto\GenericResponse;
use App\Entity\Brand\Brand;
use App\Mapper\Brand\Interface\IBrandMapper;
use App\Repository\Brand\BrandRepository;
use App\Service\Brand\Interface\IBrandService;
use App\Service\Traits\BaseServiceTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BrandService implements IBrandService
{
    use BaseServiceTrait;

    private BrandRepository $repository;
    private IBrandMapper $mapper;

    public function __construct(BrandRepository $repository, IBrandMapper $mapper)
    {
        $this->repository = $repository;
        $this->mapper = $mapper;
    }

    public function createBrand(Request $request): GenericResponse
    {
        $brandCreateIn = $this->mapper->fromCreateRequest($request);

        if (!$brandCreateIn->getBrandName() || !$brandCreateIn->getBrandImage() || !$brandCreateIn->getRating()) {
            return $this->result(
                null,
                GenericResponseStatuses::VALIDATION_ERROR,
                Response::HTTP_BAD_REQUEST,
                ['Missing required fields: brand_name, brand_image, rating']
            );
        }

        $brand = $this->mapper->toEntity($brandCreateIn);
        $this->persist($brand);

        $brandOut = $this->mapper->toOut($brand);
        return $this->result($this->mapper->toArray($brandOut));
    }

    public function findAll(): GenericResponse
    {
        $brands = $this->repository->findAllOrderedByRating();
        $result = [];

        foreach ($brands as $brand) {
            $brandOut = $this->mapper->toOut($brand);
            $result[] = $this->mapper->toArray($brandOut);
        }

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

        $brandOut = $this->mapper->toOut($brand);
        return $this->result($this->mapper->toArray($brandOut));
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

        $brandOut = $this->mapper->toOut($brand);
        return $this->result($this->mapper->toArray($brandOut));
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
}

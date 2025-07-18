<?php

namespace App\Service\TopList\Implementation;

use App\Constant\GenericResponseStatuses;
use App\Dto\GenericResponse;
use App\Entity\TopList\TopListEntry;
use App\Mapper\TopList\Interface\ITopListMapper;
use App\Repository\Brand\BrandRepository;
use App\Repository\Country\CountryRepository;
use App\Repository\TopList\TopListEntryRepository;
use App\Service\TopList\Interface\ITopListService;
use App\Service\Traits\BaseServiceTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TopListService implements ITopListService
{
    use BaseServiceTrait;

    private TopListEntryRepository $repository;
    private CountryRepository $countryRepository;
    private BrandRepository $brandRepository;
    private ITopListMapper $mapper;

    public function __construct(
        TopListEntryRepository $repository,
        CountryRepository $countryRepository,
        BrandRepository $brandRepository,
        ITopListMapper $mapper
    ) {
        $this->repository = $repository;
        $this->countryRepository = $countryRepository;
        $this->brandRepository = $brandRepository;
        $this->mapper = $mapper;
    }



    public function getTopListByGeolocation(Request $request): GenericResponse
    {
        $countryCode = $request->headers->get('CF-IPCountry');

        if ($countryCode) {
            $entries = $this->repository->findTopListByCountryCode($countryCode);
            if (!empty($entries)) {
                return $this->formatEntries($entries);
            }
        }
        // If no country code is provided, or if the country has no toplist we use default
        return $this->getDefaultTopList();
    }

    private function getDefaultTopList(): GenericResponse
    {
        // Get top 10 brands by rating
        $topBrands = $this->brandRepository->findBy([], ['rating' => 'DESC'], 10);

        if (empty($topBrands)) {
            return $this->result([]);
        }

        $defaultEntries = [];
        $position = 1;

        foreach ($topBrands as $brand) {
            $defaultEntries[] = [
                'position' => $position,
                'brand' => [
                    'brand_id' => $brand->getId(),
                    'uuid' => $brand->getUuid(),
                    'brand_name' => $brand->getBrandName(),
                    'brand_image' => $brand->getBrandImage(),
                    'rating' => $brand->getRating()
                ]
            ];
            $position++;
        }

        $result = [
            'country' => null,
            'entries' => $defaultEntries,
            'is_default' => true
        ];

        return $this->result($result);
    }

    private function formatEntries(array $entries): GenericResponse
    {
        if (empty($entries)) {
            return $this->result([]);
        }

        $country = $entries[0]->getCountry();
        $countryData = [
            'id' => $country->getId(),
            'uuid' => $country->getUuid(),
            'iso_code' => $country->getIsoCode(),
            'name' => $country->getName(),
            'is_default' => $country->isDefault()
        ];

        $toplistEntries = [];
        foreach ($entries as $entry) {
            $entryOut = $this->mapper->toOut($entry);
            $entryData = $this->mapper->toArray($entryOut);
            unset($entryData['country']);
            $toplistEntries[] = $entryData;
        }

        $result = [
            'country' => $countryData,
            'entries' => $toplistEntries
        ];

        return $this->result($result);
    }

    public function createTopListEntry(Request $request): GenericResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data || !isset($data['brand_uuid']) || !isset($data['country_uuid']) || !isset($data['position'])) {
            return $this->result(
                null, 
                GenericResponseStatuses::VALIDATION_ERROR, 
                Response::HTTP_BAD_REQUEST,
                ['Missing required fields: brand_uuid, country_uuid, position']
            );
        }

        $brand = $this->brandRepository->getByUuid($data['brand_uuid']);
        $country = $this->countryRepository->getByUuid($data['country_uuid']);

        if (!$brand || !$country) {
            return $this->result(
                null, 
                GenericResponseStatuses::NOT_FOUND, 
                Response::HTTP_NOT_FOUND,
                ['Brand or Country not found']
            );
        }

        $entry = new TopListEntry();
        $entry->setBrand($brand)
              ->setCountry($country)
              ->setPosition((int)$data['position'])
              ->setIsActive($data['is_active'] ?? true);

        $this->persist($entry);

        $entryOut = $this->mapper->toOut($entry);
        return $this->result($this->mapper->toArray($entryOut));
    }

    public function updateTopListEntry(string $uuid, Request $request): GenericResponse
    {
        $entry = $this->repository->getByUuid($uuid);

        if (!$entry) {
            return $this->result(
                null,
                GenericResponseStatuses::NOT_FOUND,
                Response::HTTP_NOT_FOUND,
                ['TopList entry not found']
            );
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['position'])) {
            $entry->setPosition((int)$data['position']);
        }
        if (isset($data['is_active'])) {
            $entry->setIsActive((bool)$data['is_active']);
        }

        $this->persist($entry);

        $entryOut = $this->mapper->toOut($entry);
        return $this->result($this->mapper->toArray($entryOut));
    }

    public function deleteTopListEntry(string $uuid): GenericResponse
    {
        $entry = $this->repository->getByUuid($uuid);

        if (!$entry) {
            return $this->result(
                null,
                GenericResponseStatuses::NOT_FOUND,
                Response::HTTP_NOT_FOUND,
                ['TopList entry not found']
            );
        }

        $this->em->remove($entry);
        $this->em->flush();

        return $this->result(['message' => 'TopList entry deleted successfully']);
    }
}

<?php

namespace App\Service\TopList\Implementation;

use App\Constant\GenericResponseStatuses;
use App\Dto\GenericResponse;
use App\Entity\TopList\TopListEntry;
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

    public function __construct(
        TopListEntryRepository $repository,
        CountryRepository $countryRepository,
        BrandRepository $brandRepository
    ) {
        $this->repository = $repository;
        $this->countryRepository = $countryRepository;
        $this->brandRepository = $brandRepository;
    }

    public function getTopListByCountry(string $countryCode): GenericResponse
    {
        $entries = $this->repository->findTopListByCountryCode($countryCode);
        
        if (empty($entries)) {
            // Fallback to default country
            $defaultCountry = $this->countryRepository->findDefaultCountry();
            if ($defaultCountry) {
                $entries = $this->repository->findTopListByCountry($defaultCountry);
            }
        }

        $result = array_map([$this, 'toTopListEntryArray'], $entries);
        
        return $this->result($result);
    }

    public function getTopListByGeolocation(Request $request): GenericResponse
    {
        // Get country from CF-IPCountry header (Cloudflare)
        $countryCode = $request->headers->get('CF-IPCountry');
        
        if (!$countryCode) {
            // Fallback to default country if no geolocation header
            $countryCode = $_ENV['DEFAULT_COUNTRY_CODE'] ?? 'FR';
        }

        return $this->getTopListByCountry($countryCode);
    }

    public function createEntry(Request $request): GenericResponse
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

        return $this->result($this->toTopListEntryArray($entry));
    }

    public function updateEntry(string $uuid, Request $request): GenericResponse
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

        return $this->result($this->toTopListEntryArray($entry));
    }

    public function deleteEntry(string $uuid): GenericResponse
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

        $this->delete($entry);

        return $this->result(['message' => 'TopList entry deleted successfully']);
    }

    private function toTopListEntryArray(TopListEntry $entry): array
    {
        return [
            'id' => $entry->getId(),
            'uuid' => $entry->getUuid(),
            'position' => $entry->getPosition(),
            'is_active' => $entry->isActive(),
            'brand' => [
                'brand_id' => $entry->getBrand()->getId(),
                'uuid' => $entry->getBrand()->getUuid(),
                'brand_name' => $entry->getBrand()->getBrandName(),
                'brand_image' => $entry->getBrand()->getBrandImage(),
                'rating' => $entry->getBrand()->getRating()
            ],
            'country' => [
                'id' => $entry->getCountry()->getId(),
                'uuid' => $entry->getCountry()->getUuid(),
                'iso_code' => $entry->getCountry()->getIsoCode(),
                'name' => $entry->getCountry()->getName()
            ],
            'created_at' => $entry->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entry->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }
}

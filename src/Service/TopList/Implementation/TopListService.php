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

    public function getTopListByCountry(string $countryCode): GenericResponse
    {
        $entries = $this->repository->findTopListByCountryCode($countryCode);

        if (!empty($entries)) {
            return $this->formatEntries($entries);
        }

        // Fallback to database default country
        $defaultCountry = $this->countryRepository->findDefaultCountry();
        if ($defaultCountry) {
            $entries = $this->repository->findTopListByCountry($defaultCountry);
            if (!empty($entries)) {
                return $this->formatEntries($entries);
            }
        }

        // Ultimate fallback - use Cameroon (CM) if no default is set
        if ($countryCode !== 'CM') {
            $entries = $this->repository->findTopListByCountryCode('CM');
            if (!empty($entries)) {
                return $this->formatEntries($entries);
            }
        }

        // If even Cameroon has no toplist, return empty
        return $this->result([]);
    }

    public function getTopListByGeolocation(Request $request): GenericResponse
    {
        // Get country from CF-IPCountry header (Cloudflare)
        $countryCode = $request->headers->get('CF-IPCountry');

        if ($countryCode) {
            $entries = $this->repository->findTopListByCountryCode($countryCode);
            if (!empty($entries)) {
                return $this->formatEntries($entries);
            }
        }

        // Fallback to database default country
        $defaultCountry = $this->countryRepository->findDefaultCountry();
        if ($defaultCountry) {
            $entries = $this->repository->findTopListByCountry($defaultCountry);
            if (!empty($entries)) {
                return $this->formatEntries($entries);
            }
        }

        // Last fallback - using my beloved Cameroon if no default is set
        $entries = $this->repository->findTopListByCountryCode('CM');
        if (!empty($entries)) {
            return $this->formatEntries($entries);
        }

        // If even Cameroon has no toplist, return empty
        return $this->result([]);
    }

    private function formatEntries(array $entries): GenericResponse
    {
        $result = [];
        foreach ($entries as $entry) {
            $entryOut = $this->mapper->toOut($entry);
            $result[] = $this->mapper->toArray($entryOut);
        }
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

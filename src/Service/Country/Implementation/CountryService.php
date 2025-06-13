<?php

namespace App\Service\Country\Implementation;

use App\Constant\GenericResponseStatuses;
use App\Dto\GenericResponse;
use App\Entity\Country\Country;
use App\Repository\Country\CountryRepository;
use App\Service\Country\Interface\ICountryService;
use App\Service\Traits\BaseServiceTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CountryService implements ICountryService
{
    use BaseServiceTrait;

    private CountryRepository $repository;

    public function __construct(CountryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(Request $request): GenericResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['iso_code']) || !isset($data['name'])) {
            return $this->result(
                null,
                GenericResponseStatuses::VALIDATION_ERROR,
                Response::HTTP_BAD_REQUEST,
                ['Missing required fields: iso_code, name']
            );
        }

        // If setting this country as default, unset all other defaults first. We don't need more than one default country 
        if ($data['is_default'] ?? false) {
            $this->unsetAllDefaults();
        }

        $country = new Country();
        $country->setIsoCode($data['iso_code'])
                ->setName($data['name'])
                ->setIsDefault($data['is_default'] ?? false);

        $this->persist($country);

        return $this->result($this->toCountryArray($country));
    }

    public function findAll(): GenericResponse
    {
        $countries = $this->repository->findAll();
        $result = array_map([$this, 'toCountryArray'], $countries);
        
        return $this->result($result);
    }

    public function findOne(string $uuid): GenericResponse
    {
        $country = $this->repository->getByUuid($uuid);
        
        if (!$country) {
            return $this->result(
                null, 
                GenericResponseStatuses::NOT_FOUND, 
                Response::HTTP_NOT_FOUND,
                ['Country not found']
            );
        }

        return $this->result($this->toCountryArray($country));
    }

    public function update(string $uuid, Request $request): GenericResponse
    {
        $country = $this->repository->getByUuid($uuid);

        if (!$country) {
            return $this->result(
                null,
                GenericResponseStatuses::NOT_FOUND,
                Response::HTTP_NOT_FOUND,
                ['Country not found']
            );
        }

        $data = json_decode($request->getContent(), true);

        // If setting this country as default, unset all other defaults first
        if (isset($data['is_default']) && (bool)$data['is_default']) {
            $this->unsetAllDefaults();
        }

        if (isset($data['iso_code'])) {
            $country->setIsoCode($data['iso_code']);
        }
        if (isset($data['name'])) {
            $country->setName($data['name']);
        }
        if (isset($data['is_default'])) {
            $country->setIsDefault((bool)$data['is_default']);
        }

        $this->persist($country);

        return $this->result($this->toCountryArray($country));
    }

    public function deleteCountry(string $uuid): GenericResponse
    {
        $country = $this->repository->getByUuid($uuid);
        
        if (!$country) {
            return $this->result(
                null, 
                GenericResponseStatuses::NOT_FOUND, 
                Response::HTTP_NOT_FOUND,
                ['Country not found']
            );
        }

        $this->delete($country);

        return $this->result(['message' => 'Country deleted successfully']);
    }

    public function getByIsoCode(string $isoCode): GenericResponse
    {
        $country = $this->repository->findByIsoCode($isoCode);
        
        if (!$country) {
            return $this->result(
                null, 
                GenericResponseStatuses::NOT_FOUND, 
                Response::HTTP_NOT_FOUND,
                ['Country not found']
            );
        }

        return $this->result($this->toCountryArray($country));
    }

    private function unsetAllDefaults(): void
    {
        $defaultCountries = $this->repository->findBy(['isDefault' => true]);
        foreach ($defaultCountries as $country) {
            $country->setIsDefault(false);
            $this->em->persist($country);
        }
        $this->em->flush();
    }

    private function toCountryArray(Country $country): array
    {
        return [
            'id' => $country->getId(),
            'uuid' => $country->getUuid(),
            'iso_code' => $country->getIsoCode(),
            'name' => $country->getName(),
            'is_default' => $country->isDefault(),
            'created_at' => $country->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $country->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }
}

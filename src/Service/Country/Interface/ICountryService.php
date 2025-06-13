<?php

namespace App\Service\Country\Interface;

use App\Dto\GenericResponse;
use Symfony\Component\HttpFoundation\Request;

interface ICountryService
{
    public function createCountry(Request $request): GenericResponse;

    public function findAllCountries(): GenericResponse;

    public function findCountryByUuid(string $uuid): GenericResponse;

    public function updateCountry(string $uuid, Request $request): GenericResponse;
    
    public function deleteCountry(string $uuid): GenericResponse;
    
    public function getCountryByIsoCode(string $isoCode): GenericResponse;
}

<?php

namespace App\Service\Country\Interface;

use App\Dto\GenericResponse;
use Symfony\Component\HttpFoundation\Request;

interface ICountryService
{
    public function create(Request $request): GenericResponse;
    
    public function findAll(): GenericResponse;
    
    public function findOne(string $uuid): GenericResponse;
    
    public function update(string $uuid, Request $request): GenericResponse;
    
    public function deleteCountry(string $uuid): GenericResponse;
    
    public function getByIsoCode(string $isoCode): GenericResponse;
}

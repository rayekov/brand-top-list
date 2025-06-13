<?php

namespace App\Service\TopList\Interface;

use App\Dto\GenericResponse;
use Symfony\Component\HttpFoundation\Request;

interface ITopListService
{
    public function getTopListByCountry(string $countryCode): GenericResponse;
    
    public function getTopListByGeolocation(Request $request): GenericResponse;
    
    public function createEntry(Request $request): GenericResponse;
    
    public function updateEntry(string $uuid, Request $request): GenericResponse;
    
    public function deleteEntry(string $uuid): GenericResponse;
}

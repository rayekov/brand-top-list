<?php

namespace App\Service\TopList\Interface;

use App\Dto\GenericResponse;
use Symfony\Component\HttpFoundation\Request;

interface ITopListService
{
    public function getTopListByCountry(string $countryCode): GenericResponse;

    public function getTopListByGeolocation(Request $request): GenericResponse;

    public function createTopListEntry(Request $request): GenericResponse;

    public function updateTopListEntry(string $uuid, Request $request): GenericResponse;

    public function deleteTopListEntry(string $uuid): GenericResponse;
}

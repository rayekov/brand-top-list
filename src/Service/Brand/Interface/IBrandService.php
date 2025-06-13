<?php

namespace App\Service\Brand\Interface;

use App\Dto\GenericResponse;
use Symfony\Component\HttpFoundation\Request;

interface IBrandService
{
    public function createBrand(Request $request): GenericResponse;
    
    public function findAll(): GenericResponse;
    
    public function findOne(string $uuid): GenericResponse;
    
    public function updateBrand(string $uuid, Request $request): GenericResponse;
    
    public function deleteBrand(string $uuid): GenericResponse;
}

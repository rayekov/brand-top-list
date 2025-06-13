<?php

namespace App\Mapper\Country\Interface;

use App\Dto\Country\Out\CountryOut;
use App\Entity\Country\Country;
use App\Mapper\IBaseMapper;

interface ICountryMapper extends IBaseMapper
{
    public function toOut(Country $entity): CountryOut;
    
    public function toArray(CountryOut $dto): array;
}

<?php

namespace App\Mapper\TopList\Interface;

use App\Dto\TopList\Out\TopListEntryOut;
use App\Entity\TopList\TopListEntry;
use App\Mapper\IBaseMapper;

interface ITopListMapper extends IBaseMapper
{
    public function toOut(TopListEntry $entity): TopListEntryOut;
    
    public function toArray(TopListEntryOut $dto): array;
}

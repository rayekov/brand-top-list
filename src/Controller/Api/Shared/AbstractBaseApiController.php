<?php

namespace App\Controller\Api\Shared;

use App\Dto\GenericResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;

abstract class AbstractBaseApiController extends AbstractFOSRestController
{
    public function result(GenericResponse $result)
    {
        return $this->view($result->toArray(), $result->getCode());
    }
}

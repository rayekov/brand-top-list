<?php

namespace App\Service\Traits;

use App\Constant\GenericResponseStatuses;
use App\Dto\GenericResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

trait BaseServiceTrait
{
    #[Required]
    public EntityManagerInterface $em;

    public function result($data = null, $status = GenericResponseStatuses::SUCCESS, $code = Response::HTTP_OK, $messages = [])
    {
        return new GenericResponse($data, $status, $code, $messages);
    }

    public function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }

    public function delete($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
        return true;
    }
}

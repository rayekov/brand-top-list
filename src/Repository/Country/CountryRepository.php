<?php

namespace App\Repository\Country;

use App\Entity\Country\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function save(Country $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Country $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByUuid(string $uuid): ?Country
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    public function findByIsoCode(string $isoCode): ?Country
    {
        return $this->findOneBy(['isoCode' => strtoupper($isoCode)]);
    }

    public function findDefaultCountry(): ?Country
    {
        return $this->findOneBy(['isDefault' => true]);
    }
}

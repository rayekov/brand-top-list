<?php

namespace App\Repository\TopList;

use App\Entity\TopList\TopListEntry;
use App\Entity\Country\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TopListEntry>
 */
class TopListEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopListEntry::class);
    }

    public function save(TopListEntry $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TopListEntry $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByUuid(string $uuid): ?TopListEntry
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    public function findTopListByCountry(Country $country): array
    {
        return $this->createQueryBuilder('tle')
            ->innerJoin('tle.brand', 'b')
            ->where('tle.country = :country')
            ->andWhere('tle.isActive = :active')
            ->setParameter('country', $country)
            ->setParameter('active', true)
            ->orderBy('tle.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTopListByCountryCode(string $countryCode): array
    {
        return $this->createQueryBuilder('tle')
            ->innerJoin('tle.brand', 'b')
            ->innerJoin('tle.country', 'c')
            ->where('c.isoCode = :countryCode')
            ->andWhere('tle.isActive = :active')
            ->setParameter('countryCode', strtoupper($countryCode))
            ->setParameter('active', true)
            ->orderBy('tle.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

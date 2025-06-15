<?php

namespace App\Repository\Brand;

use App\Entity\Brand\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Brand>
 */
class BrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Brand::class);
    }

    public function save(Brand $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Brand $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByUuid(string $uuid): ?Brand
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    public function findAllOrderedByRating(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.rating', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

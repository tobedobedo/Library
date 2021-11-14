<?php

namespace App\Repository;

use App\Entity\PublishingHouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PublishingHouse|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublishingHouse|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublishingHouse[]    findAll()
 * @method PublishingHouse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublishingHouseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublishingHouse::class);
    }

    // /**
    //  * @return PublishingHouse[] Returns an array of PublishingHouse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PublishingHouse
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * @param array $filters
     * @return array
     */
    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('ph')
            ->select('ph.id', 'ph.address as address', 'ph.name as name', 'GROUP_CONCAT(book.id) as books')
            ->leftJoin('ph.books', 'book')
        ;

        foreach ($filters as $field => $value) {
            $qb
                ->andWhere($qb->expr()->eq($field, ':x'))
                ->setParameter(':x', $value)
            ;
        }

        $result = $qb
            ->groupBy('ph.id')
            ->getQuery()
            ->getResult()
        ;

        foreach ($result as &$ph) {
            $ph['books'] = explode(',', $ph['books'] ?? '');
        }

        return $result;
    }
}

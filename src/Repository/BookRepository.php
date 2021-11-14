<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

     /**
      * @return Book[] Returns an array of Book objects
      */
    public function findByYearField(int $value): array
    {
        return $this->createQueryBuilder('b')
            ->select('b.id', 'b.name')
            ->andWhere('b.year > :x')
            ->setParameter('x', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param array $filters
     * @return array
     */
    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b.id', 'b.name as book_name', 'b.year', 'p.name as pub_house_name', 'a.surname as author_surname', 'a.name as author_name')
            ->leftJoin('b.pubHouse', 'p')
            ->leftJoin('b.authors', 'a')
        ;

        foreach ($filters as $field => $value) {
            $qb
                ->andWhere($qb->expr()->eq($field, ':x'))
                ->setParameter(':x', $value)
            ;
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}

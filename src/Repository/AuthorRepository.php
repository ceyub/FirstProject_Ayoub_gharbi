<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    //    /**
    //     * @return Author[] Returns an array of Author objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Author
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function ShowAllAuthors(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.username', 'ASC')
            ->andWhere('a.email : LIKE val ')
            ->setParameter('val', ' %a%')
            ->getQuery()
            ->getResult();
    }
    public function findAuthorsWithMoreThanXBooks(int $minBooks)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.nb_books > :minBooks')
            ->setParameter('minBooks', $minBooks)
            ->orderBy('a.nb_books', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
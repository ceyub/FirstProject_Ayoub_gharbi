<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Rechercher un livre par référence
     */
    public function searchBookByRef(string $ref)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Liste des livres triés par auteur
     */
    public function booksListByAuthors()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->orderBy('a.username', 'ASC')
            ->addOrderBy('b.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Livres publiés avant 2023 dont l'auteur a plus de 10 livres
     */
    public function findBooksBefore2023AuthorMoreThan10Books()
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.author', 'a')
            ->andWhere('b.publicationDate < :date')
            ->andWhere('a.nb_books > :minBooks')
            ->setParameter('date', new \DateTime('2023-01-01'))
            ->setParameter('minBooks', 10)
            ->orderBy('b.publicationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Mettre à jour la catégorie Science-Fiction vers Romance
     */
    public function updateScienceFictionToRomance()
    {
        return $this->createQueryBuilder('b')
            ->update()
            ->set('b.category', ':newCategory')
            ->where('b.category = :oldCategory')
            ->setParameter('newCategory', 'Romance')
            ->setParameter('oldCategory', 'Science-Fiction')
            ->getQuery()
            ->execute();
    }
}
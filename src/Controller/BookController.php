<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/books', name: 'app_books')]
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        $searchRef = $request->query->get('search', '');
        $books = [];

        if ($searchRef) {
            // Recherche par référence
            $book = $bookRepository->searchBookByRef($searchRef);
            if ($book) {
                $books = [$book];
            }
        } else {
            // Tous les livres publiés
            $books = $bookRepository->findBy(['published' => true]);
        }

        $publishedBooks = $bookRepository->findBy(['published' => true]);
        $unpublishedBooks = $bookRepository->findBy(['published' => false]);
        
        return $this->render('book/index.html.twig', [
            'books' => $books,
            'published_books' => $publishedBooks,
            'unpublished_books' => $unpublishedBooks,
            'published_count' => count($publishedBooks),
            'unpublished_count' => count($unpublishedBooks),
            'search_ref' => $searchRef,
        ]);
    }

    #[Route('/books/by-author', name: 'books_by_author')]
    public function booksByAuthor(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->booksListByAuthors();
        
        return $this->render('book/books_by_author.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/books/before-2023', name: 'books_before_2023')]
    public function booksBefore2023(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findBooksBefore2023AuthorMoreThan10Books();
        
        return $this->render('book/books_before_2023.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/books/update-category', name: 'update_category')]
    public function updateCategory(BookRepository $bookRepository): Response
    {
        $updatedCount = $bookRepository->updateScienceFictionToRomance();
        
        $this->addFlash('success', "{$updatedCount} livres mis à jour de Science-Fiction vers Romance");
        return $this->redirectToRoute('app_books');
    }

    #[Route('/book/new', name: 'book_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, AuthorRepository $authorRepository): Response
    {
        if ($request->isMethod('POST')) {
            $book = new Book();
            $book->setRef($request->request->get('ref'));
            $book->setTitle($request->request->get('title'));
            $book->setCategory($request->request->get('category'));
            $book->setPublished(true); // Toujours true comme demandé
            $book->setPublicationDate(new \DateTime($request->request->get('publication_date')));
            
            $authorId = $request->request->get('author');
            $author = $authorRepository->find($authorId);
            $book->setAuthor($author);
            
            // Incrémenter nb_books de l'auteur
            if ($author) {
                $author->setNbBooks($author->getNbBooks() + 1);
            }

            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Livre ajouté avec succès!');
            return $this->redirectToRoute('app_books');
        }

        $authors = $authorRepository->findAll();
        return $this->render('book/new.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/book/{id}/edit', name: 'book_edit')]
    public function edit(Request $request, int $id, BookRepository $bookRepository, AuthorRepository $authorRepository, EntityManagerInterface $entityManager): Response
    {
        $book = $bookRepository->find($id);
        
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        if ($request->isMethod('POST')) {
            $book->setRef($request->request->get('ref'));
            $book->setTitle($request->request->get('title'));
            $book->setCategory($request->request->get('category'));
            $book->setPublicationDate(new \DateTime($request->request->get('publication_date')));
            
            $authorId = $request->request->get('author');
            $author = $authorRepository->find($authorId);
            $book->setAuthor($author);

            $entityManager->flush();

            $this->addFlash('success', 'Livre modifié avec succès!');
            return $this->redirectToRoute('app_books');
        }

        $authors = $authorRepository->findAll();
        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'authors' => $authors,
        ]);
    }

    #[Route('/book/{id}/delete', name: 'book_delete')]
    public function delete(Request $request, int $id, BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {
        $book = $bookRepository->find($id);
        
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            // Décrémenter nb_books de l'auteur
            $author = $book->getAuthor();
            if ($author) {
                $author->setNbBooks(max(0, $author->getNbBooks() - 1));
            }

            $entityManager->remove($book);
            $entityManager->flush();
            
            $this->addFlash('success', 'Livre supprimé avec succès!');
        }

        return $this->redirectToRoute('app_books');
    }

    #[Route('/book/{id}', name: 'book_show')]
    public function show(int $id, BookRepository $bookRepository): Response
    {
        $book = $bookRepository->find($id);
        
        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
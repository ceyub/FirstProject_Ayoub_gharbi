<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/author/{name}', name: 'show_author')]
    public function showAuthor(string $name): Response
    {
        return $this->render('author/show.html.twig', [
            'name' => $name,
        ]);
    }
    #[Route('/authors/by-email', name: 'authors_by_email')]
    public function listAuthorsByEmail(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->listAuthorByEmail();
        
        return $this->render('author/list_by_email.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/details/{id}', name: 'author_details')]
    public function authorDetails(int $id, AuthorRepository $authorRepository): Response
    {
        $author = $authorRepository->find($id);

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        return $this->render('author/showAuthor.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/authors', name: 'list_authors')]
    public function listAuthors(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAll();
        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/showAll', name: 'showAll')]
    public function showAll(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->findAll();
        return $this->render("author/showAll.html.twig", ['list' => $authors]);
    }

    // AJOUT D'AUTEUR SANS FORMULAIRE (avec données statiques)
    #[Route('/author/add/static', name: 'author_add_static')]
    public function addStatic(EntityManagerInterface $entityManager): Response
    {
        $author = new Author();
        $author->setUsername('Nouvel Auteur');
        $author->setEmail('nouvel.auteur@email.com');
        $author->setNbBooks(0);

        $entityManager->persist($author);
        $entityManager->flush();

        $this->addFlash('success', 'Auteur ajouté avec succès!');
        return $this->redirectToRoute('showAll');
    }

    // AJOUT D'AUTEUR AVEC FORMULAIRE HTML SIMPLE
    #[Route('/author/new', name: 'author_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $author = new Author();
            $author->setUsername($request->request->get('username'));
            $author->setEmail($request->request->get('email'));
            $author->setNbBooks((int)$request->request->get('nb_books', 0));

            $entityManager->persist($author);
            $entityManager->flush();

            $this->addFlash('success', 'Auteur créé avec succès!');
            return $this->redirectToRoute('showAll');
        }

        return $this->render('author/new.html.twig');
    }

    // MODIFICATION D'AUTEUR AVEC FORMULAIRE HTML SIMPLE
    #[Route('/author/{id}/edit', name: 'author_edit')]
    public function edit(Request $request, int $id, AuthorRepository $authorRepository, EntityManagerInterface $entityManager): Response
    {
        $author = $authorRepository->find($id);
        
        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        if ($request->isMethod('POST')) {
            $author->setUsername($request->request->get('username'));
            $author->setEmail($request->request->get('email'));
            $author->setNbBooks((int)$request->request->get('nb_books'));

            $entityManager->flush();

            $this->addFlash('success', 'Auteur modifié avec succès!');
            return $this->redirectToRoute('showAll');
        }

        return $this->render('author/edit.html.twig', [
            'author' => $author,
        ]);
    }

    // SUPPRESSION D'AUTEUR
    #[Route('/author/{id}/delete', name: 'author_delete')]
    public function delete(Request $request, int $id, AuthorRepository $authorRepository, EntityManagerInterface $entityManager): Response
    {
        $author = $authorRepository->find($id);
        
        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            $entityManager->remove($author);
            $entityManager->flush();
            
            $this->addFlash('success', 'Auteur supprimé avec succès!');
        }

        return $this->redirectToRoute('showAll');
    
}

}
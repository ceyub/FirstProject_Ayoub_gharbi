namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/author')]
class AuthorController extends AbstractController
{
    #[Route('/', name: 'author_index')]
    public function index(AuthorRepository $repo): Response
    {
        $authors = $repo->findAll();
        return $this->render('author/index.html.twig', compact('authors'));
    }

    #[Route('/new', name: 'author_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('author_index');
        }
        return $this->render('author/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'author_edit')]
    public function edit(Author $author, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('author_index');
        }
        return $this->render('author/edit.html.twig', ['form' => $form->createView(), 'author' => $author]);
    }

    #[Route('/{id}/delete', name: 'author_delete')]
    public function delete(Author $author, EntityManagerInterface $em): Response
    {
        $em->remove($author);
        $em->flush();
        return $this->redirectToRoute('author_index');
    }
}

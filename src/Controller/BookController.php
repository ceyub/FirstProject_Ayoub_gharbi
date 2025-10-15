namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/', name: 'book_index')]
    public function index(BookRepository $repo): Response
    {
        $published = $repo->findBy(['published' => true]);
        $unpublished = $repo->findBy(['published' => false]);
        return $this->render('book/index.html.twig', [
            'published' => $published,
            'unpublished' => $unpublished
        ]);
    }

    #[Route('/new', name: 'book_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $book->setPublished(true);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $author = $book->getAuthor();
            $author->incrementNbBooks();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('book_index');
        }
        return $this->render('book/new.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'book_edit')]
    public function edit(Book $book, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $oldAuthor = $book->getAuthor();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newAuthor = $book->getAuthor();
            if ($oldAuthor !== $newAuthor) {
                $oldAuthor->decrementNbBooks();
                $newAuthor->incrementNbBooks();
            }
            $em->flush();
            return $this->redirectToRoute('book_index');
        }
        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book
        ]);
    }

    #[Route('/{id}/delete', name: 'book_delete')]
    public function delete(Book $book, EntityManagerInterface $em): Response
    {
        $author = $book->getAuthor();
        $author->decrementNbBooks();
        $em->remove($book);
        $em->flush();

        $authors = $em->getRepository(\App\Entity\Author::class)->findBy(['nb_books' => 0]);
        foreach ($authors as $a) {
            $em->remove($a);
        }
        $em->flush();

        return $this->redirectToRoute('book_index');
    }

    #[Route('/{id}', name: 'book_show')]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', ['book' => $book]);
    }
}

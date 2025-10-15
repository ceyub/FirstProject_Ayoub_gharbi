<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    private $id;

    /** @ORM\Column(type="string", length=255) */
    private $name;

    /** @ORM\Column(type="integer") */
    private $nb_books = 0;

    /** @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="author", orphanRemoval=true) */
    private $books;

    public function __construct(){ $this->books = new ArrayCollection(); }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getNbBooks(): int { return $this->nb_books; }
    public function setNbBooks(int $n): self { $this->nb_books = $n; return $this; }
    public function incrementNbBooks(): self{ $this->nb_books++; return $this; }
    public function decrementNbBooks(): self{ if($this->nb_books>0) $this->nb_books--; return $this; }

    /** @return Collection|Book[] */
    public function getBooks(): Collection { return $this->books; }
}

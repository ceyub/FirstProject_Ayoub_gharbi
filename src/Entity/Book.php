<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    private $id;

    /** @ORM\Column(type="string", length=255) */
    private $title;

    /** @ORM\Column(type="string", length=50) */
    private $category;

    /** @ORM\Column(type="boolean") */
    private $published = true;

    /** @ORM\ManyToOne(targetEntity="App\Entity\Author", inversedBy="books")
     *  @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $t): self { $this->title = $t; return $this; }

    public function getCategory(): ?string { return $this->category; }
    public function setCategory(string $c): self { $this->category = $c; return $this; }

    public function isPublished(): bool { return $this->published; }
    public function setPublished(bool $p): self { $this->published = $p; return $this; }

    public function getAuthor(): ?Author { return $this->author; }
    public function setAuthor(?Author $a): self { $this->author = $a; return $this; }
}

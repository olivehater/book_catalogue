<?php
/**
 * Book entity.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @ORM\Table(name="books")
 */
class Book
{
    /**
     * Primary key.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Created at.
     *
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * Updated at.
     *
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * Title.
     *
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     length=255)
     */
    private $title;

    /**
     * Description.
     *
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * Category.
     *
     * @var \App\Entity\Category Category
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Category",
     *     inversedBy="books"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * Author.
     *
     * @var \App\Entity\Author Author
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Author",
     *     inversedBy="books"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * Tags.
     *
     * @var array
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\Tag",
     *     inversedBy="books",
     *     orphanRemoval=true
     * )
     * @ORM\JoinTable(name="books_tags")
     */
    private $tags;

    /**
     * Favourite.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Favourite", mappedBy="book")
     */
    private $favourite;

    /**
     * Comment.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="book")
     */
    private $comment;

    /**
     * Book constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->favourite = new ArrayCollection();
        $this->comment = new ArrayCollection();
    }

    /**
     * Getter for id.
     *
     * @return int|null Result
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for Created At.
     *
     * @return \DateTimeInterface|null Created at
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for Created At.
     *
     * @param \DateTimeInterface $createdAt Created at
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for Updated At.
     *
     * @return \DateTimeInterface|null Updated at
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Setter for Updated At.
     *
     * @param \DateTimeInterface $updatedAt Updated at
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for Title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for Title.
     *
     * @param string $title Title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Getter for Description.
     *
     * @return string|null Description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setter for Description.
     *
     * @param string $description Description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Getter for category.
     *
     * @return \App\Entity\Category|null Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param \App\Entity\Category|null $category Category
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * Getter for author.
     *
     * @return \App\Entity\Author|null Author
     */
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param \App\Entity\Author|null $author Author
     */
    public function setAuthor(?Author $author): void
    {
        $this->author = $author;
    }

    /**
     * Getter for tags.
     *
     * @return \Doctrine\Common\Collections\Collection|\App\Entity\Tag[] Tags collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add tag to collection.
     *
     * @param \App\Entity\Tag $tag Tag entity
     */
    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * Remove tag from collection.
     *
     * @param \App\Entity\Tag $tag Tag entity
     */
    public function removeTag(Tag $tag): void
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
    }

    /**
     * Getter for Favourite.
     *
     * @return Collection|Favourite[]
     */
    public function getFavourite(): Collection
    {
        return $this->favourite;
    }

    /**
     * Add favourite.
     *
     * @param Favourite $favourite Favourite
     */
    public function addFavourite(Favourite $favourite): void
    {
        if (!$this->favourite->contains($favourite)) {
            $this->favourite[] = $favourite;
            $favourite->setBook($this);
        }
    }

    /**
     * Remove favourite.
     *
     * @param Favourite $favourite Favourite
     */
    public function removeFavourite(Favourite $favourite): void
    {
        if ($this->favourite->contains($favourite)) {
            $this->favourite->removeElement($favourite);
            // set the owning side to null (unless already changed)
            if ($favourite->getBook() === $this) {
                $favourite->setBook(null);
            }
        }
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comment->contains($comment)) {
            $this->comment[] = $comment;
            $comment->setBook($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comment->contains($comment)) {
            $this->comment->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getBook() === $this) {
                $comment->setBook(null);
            }
        }

        return $this;
    }

}

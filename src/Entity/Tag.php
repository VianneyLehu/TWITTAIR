<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'tag_id', targetEntity: Post::class)]
    private Collection $post_id;

    public function __construct()
    {
        $this->post_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPostId(): Collection
    {
        return $this->post_id;
    }

    public function addPostId(Post $postId): self
    {
        if (!$this->post_id->contains($postId)) {
            $this->post_id->add($postId);
            $postId->setTagId($this);
        }

        return $this;
    }

    public function removePostId(Post $postId): self
    {
        if ($this->post_id->removeElement($postId)) {
            // set the owning side to null (unless already changed)
            if ($postId->getTagId() === $this) {
                $postId->setTagId(null);
            }
        }

        return $this;
    }
}

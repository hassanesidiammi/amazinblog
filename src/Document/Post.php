<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
class Post
{
    #[ODM\Id(strategy: "INCREMENT")]
    private ?int $id = null;

    #[ODM\Field(type: 'string')]
    private ?string $title = null;

    #[ODM\Field(type: 'string')]
    private ?string $content = null;

    #[ODM\Field(type: 'date')]
    private \DateTime $createdAt;

    #[ODM\ReferenceOne(targetDocument: User::class, inversedBy: 'posts')]
    private ?User $owner = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUsername(): ?string
    {
        return $this->owner?->getName();
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $user): static
    {
        $this->owner = $user;
        $user->addPost($this);

        return $this;
    }
}

<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document]
class Post
{
    #[ODM\Id(strategy: "INCREMENT")]
    #[Groups(['post:read', 'post:list'])]
    private ?int $id = null;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['post:read', 'post:list', 'post:write'])]
    private ?string $title = null;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 100, max: 300)]
    #[Groups(['post:read', 'post:write'])]
    private ?string $content = null;

    #[ODM\Field(type: 'date')]
    #[Groups(['post:read', 'post:list'])]
    private \DateTime $createdAt;

    #[ODM\ReferenceOne(targetDocument: User::class, inversedBy: 'posts')]
    #[Groups(['post:read', 'post:list'])]
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

    #[Groups(['post:read', 'post:list'])]
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

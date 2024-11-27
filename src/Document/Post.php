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
    #[Assert\Regex(
        pattern: "/<script/i",
        match: false,
        message: "Balise javascript non permise!"
    )]
    #[Groups(['post:read', 'post:list', 'post:write'])]
    private ?string $title = null;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 3000)]
    #[Assert\Regex(
        pattern: "/<script/i",
        match: false,
        message: "Balise javascript non permise!"
    )]
    #[Groups(['post:read', 'post:write'])]
    private ?string $content = null;

    #[ODM\Field(type: 'date')]
    #[Groups(['post:read', 'post:list'])]
    private \DateTime $createdAt;

    #[ODM\ReferenceOne(targetDocument: User::class, inversedBy: 'posts')]
    #[Groups(['post:read', 'post:list'])]
    private ?User $author = null;

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
        return $this->author?->getName();
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $user): static
    {
        $this->author = $user;
        $user->addPost($this);

        return $this;
    }
}

<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ODM\Document]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ODM\Id(strategy: "INCREMENT")]
    private ?int $id = null;

    #[ODM\Field(type: 'string')]
    private ?string $name = null;

    #[ODM\Field(type: 'string')]
    private ?string $email = null;

    #[ODM\Field(type: 'collection')]
    private array $roles = [];

    #[ODM\Field(type: 'string')]
    private ?string $password = null;

    #[ODM\ReferenceMany(targetDocument: Post::class, mappedBy: 'user')]
    private ArrayCollection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getPosts(): array|ArrayCollection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
        }

        if ($post->getOwner() !== $this) {
            $post->setOwner($this);
        }

        return $this;
    }
}

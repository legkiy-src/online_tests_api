<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(unique: true, nullable: false)]
    private User $user;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(targetEntity: Attempt::class, mappedBy: 'student')]
    private Collection $attempts;

    public function __construct(User $user, ?string $studentId = null)
    {
        $this->user = $user;
        $this->createdAt = new \DateTimeImmutable();
        $this->attempts = new ArrayCollection();
        $user->setRoles(array_merge($user->getRoles(), ['ROLE_STUDENT']));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAttempts(): Collection
    {
        return $this->attempts;
    }

    public function setAttempts(Collection $attempts): static
    {
        $this->attempts = $attempts;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->user->getEmail();
    }

    public function getFirstName(): string
    {
        return $this->user->getFirstName();
    }

    public function getLastName(): string
    {
        return $this->user->getLastName();
    }

    public function getFullName(): string
    {
        return $this->user->getFullName();
    }
}

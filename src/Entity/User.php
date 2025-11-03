<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\User\UserRole;
use App\Enum\User\UserStatus;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    private ?string $lastName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $patronymic = null;

    #[ORM\Column(type: Types::STRING, enumType: UserRole::class)]
    private UserRole $role;

    #[ORM\Column(type: Types::STRING, enumType: UserStatus::class)]
    private UserStatus $status;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\OneToMany(targetEntity: Attempt::class, mappedBy: 'user')]
    private Collection $attempts;

    #[ORM\OneToMany(targetEntity: Subject::class, mappedBy: 'teacher')]
    private Collection $taughtSubjects;

    public function __construct()
    {
        $this->role = UserRole::STUDENT;
        $this->status = UserStatus::ACTIVE;
        $this->createdAt = new \DateTimeImmutable();
        $this->attempts = new ArrayCollection();
        $this->taughtSubjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPatronymic(): string
    {
        return $this->patronymic;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName . ' ' . $this->patronymic;
    }

    public function getRoles(): array
    {
        return [$this->role->value];
    }

    public function getUserRole(): UserRole
    {
        return $this->role;
    }

    public function setUserRole(UserRole $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function setStatus(UserStatus $status): static
    {
        $this->status = $status;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeImmutable $lastLoginAt): static
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getAttempts(): Collection
    {
        return $this->attempts;
    }

    public function addAttempt(Attempt $attempt): static
    {
        if (!$this->attempts->contains($attempt)) {
            $this->attempts->add($attempt);
            $attempt->setUser($this);
        }

        return $this;
    }

    public function removeAttempt(Attempt $attempt): static
    {
        if ($this->attempts->removeElement($attempt)) {
            if ($attempt->getUser() === $this) {
                $attempt->setUser(null);
            }
        }

        return $this;
    }

    public function getTaughtSubjects(): Collection
    {
        return $this->taughtSubjects;
    }

    public function addTaughtSubject(Subject $taughtSubject): static
    {
        if (!$this->taughtSubjects->contains($taughtSubject)) {
            $this->taughtSubjects->add($taughtSubject);
            $taughtSubject->setTeacher($this);
        }

        return $this;
    }

    public function removeTaughtSubject(Subject $taughtSubject): static
    {
        if ($this->taughtSubjects->removeElement($taughtSubject)) {
            if ($taughtSubject->getTeacher() === $this) {
                $taughtSubject->setTeacher(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    public function eraseCredentials(): void
    {
    }
}

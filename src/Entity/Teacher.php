<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
class Teacher
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(unique: true, nullable: false)]
    private User $user;

    #[ORM\OneToMany(targetEntity: Subject::class, mappedBy: 'teacher')]
    private Collection $subjects;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->subjects = new ArrayCollection();

        $user->setRoles(array_merge($user->getRoles(), ['ROLE_TEACHER']));
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

    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function setSubjects(Collection $subjects): static
    {
        $this->subjects = $subjects;

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

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

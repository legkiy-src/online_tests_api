<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description;

    #[ORM\OneToMany(targetEntity: Attempt::class, mappedBy: 'subject')]
    private Collection $attempts;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'subject')]
    private Collection $questions;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'teacher_id', nullable: true)]
    private ?User $teacher = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isActive = true;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $defaultTimeLimit = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $name,
        ?string $description = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->attempts = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function create(
        string $name,
        ?string $description = null
    ): self {
        return new self($name, $description);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAttempts(): ?Collection
    {
        return $this->attempts;
    }

    public function addAttempt(Attempt $attempt): static
    {
        if (!$this->attempts->contains($attempt)) {
            $this->attempts->add($attempt);
            $attempt->setSubject($this);
        }

        return $this;
    }

    public function removeAttempt(Attempt $attempt): static
    {
        if ($this->attempts->removeElement($attempt)) {
            if ($attempt->getSubject() === $this) {
                $attempt->setSubject(null);
            }
        }

        return $this;
    }

    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): static
    {
        $this->teacher = $teacher;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getDefaultTimeLimit(): ?int
    {
        return $this->defaultTimeLimit;
    }

    public function setDefaultTimeLimit(?int $defaultTimeLimit): static
    {
        $this->defaultTimeLimit = $defaultTimeLimit;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

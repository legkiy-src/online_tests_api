<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    private string $title;

    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Subject $subject;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $instructions = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isActive = true;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $timeLimit = null; // В минутах

    #[ORM\Column(type: Types::INTEGER)]
    private int $maxAttempts = 1;

    #[ORM\Column(type: Types::INTEGER)]
    private int $passingScore = 60; // Процент для зачета

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $shuffleQuestions = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $shuffleAnswers = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $showResults = true;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $showCorrectAnswers = false;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $availableFrom = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $availableUntil = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'test', orphanRemoval: true)]
    #[ORM\OrderBy(['orderIndex' => 'ASC'])]
    private Collection $questions;

    #[ORM\OneToMany(targetEntity: Attempt::class, mappedBy: 'test')]
    private Collection $attempts;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(string $title, Subject $subject, User $author)
    {
        $this->title = $title;
        $this->subject = $subject;
        $this->author = $author;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->questions = new ArrayCollection();
        $this->attempts = new ArrayCollection();
    }

    public function create(string $title, Subject $subject, User $author): self
    {
        return new self($title, $subject, $author);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(string $instructions): static
    {
        $this->instructions = $instructions;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getTimeLimit(): ?int
    {
        return $this->timeLimit;
    }

    public function setTimeLimit(int $timeLimit): static
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    public function getMaxAttempts(): ?int
    {
        return $this->maxAttempts;
    }

    public function setMaxAttempts(?int $maxAttempts): static
    {
        $this->maxAttempts = $maxAttempts;

        return $this;
    }

    public function getPassingScore(): ?int
    {
        return $this->passingScore;
    }

    public function setPassingScore(?int $passingScore): static
    {
        $this->passingScore = $passingScore;

        return $this;
    }

    public function isShuffleQuestions(): ?bool
    {
        return $this->shuffleQuestions;
    }

    public function setShuffleQuestions(bool $shuffleQuestions): static
    {
        $this->shuffleQuestions = $shuffleQuestions;

        return $this;
    }

    public function isShuffleAnswers(): ?bool
    {
        return $this->shuffleAnswers;
    }

    public function setShuffleAnswers(bool $shuffleAnswers): static
    {
        $this->shuffleAnswers = $shuffleAnswers;

        return $this;
    }

    public function isShowResults(): ?bool
    {
        return $this->showResults;
    }

    public function setShowResults(bool $showResults): static
    {
        $this->showResults = $showResults;

        return $this;
    }

    public function isShowCorrectAnswers(): ?bool
    {
        return $this->showCorrectAnswers;
    }

    public function setShowCorrectAnswers(bool $showCorrectAnswers): static
    {
        $this->showCorrectAnswers = $showCorrectAnswers;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAvailableFrom(): ?\DateTimeImmutable
    {
        return $this->availableFrom;
    }

    public function setAvailableFrom(\DateTimeImmutable $availableFrom): static
    {
        $this->availableFrom = $availableFrom;

        return $this;
    }

    public function getAvailableUntil(): ?\DateTimeImmutable
    {
        return $this->availableUntil;
    }

    public function setAvailableUntil(\DateTimeImmutable $availableUntil): static
    {
        $this->availableUntil = $availableUntil;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setTest($this);
        }

        return $this;
    }

    public function addAttempts(Attempt $attempt): static
    {
        if (!$this->attempts->contains($attempt)) {
            $this->attempts->add($attempt);
            $attempt->setTest($this);
        }

        return $this;
    }
}

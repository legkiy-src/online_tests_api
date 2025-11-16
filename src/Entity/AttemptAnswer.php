<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AttemptAnswerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttemptAnswerRepository::class)]
class AttemptAnswer
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Attempt::class, inversedBy: 'attemptAnswers')]
    #[ORM\JoinColumn(nullable: false)]
    private Attempt $attempt;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Question $question;

    #[ORM\ManyToMany(targetEntity: Answer::class)]
    #[ORM\JoinTable(name: 'attempt_answer_selections')]
    private Collection $selectedAnswers;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $customAnswer = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $answeredAt;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $pointsAwarded = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $teacherComment = null;

    public function __construct()
    {
        $this->selectedAnswers = new ArrayCollection();
        $this->answeredAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttempt(): Attempt
    {
        return $this->attempt;
    }

    public function setAttempt(Attempt $attempt): static
    {
        $this->attempt = $attempt;

        return $this;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getSelectedAnswers(): Collection
    {
        return $this->selectedAnswers;
    }

    public function setSelectedAnswers(Collection $selectedAnswers): static
    {
        $this->selectedAnswers = $selectedAnswers;

        return $this;
    }

    public function getCustomAnswer(): ?string
    {
        return $this->customAnswer;
    }

    public function setCustomAnswer(?string $customAnswer): static
    {
        $this->customAnswer = $customAnswer;

        return $this;
    }

    public function getAnsweredAt(): \DateTimeImmutable
    {
        return $this->answeredAt;
    }

    public function setAnsweredAt(\DateTimeImmutable $answeredAt): static
    {
        $this->answeredAt = $answeredAt;

        return $this;
    }

    public function getPointsAwarded(): ?int
    {
        return $this->pointsAwarded;
    }

    public function setPointsAwarded(?int $pointsAwarded): static
    {
        $this->pointsAwarded = $pointsAwarded;

        return $this;
    }

    public function getTeacherComment(): ?string
    {
        return $this->teacherComment;
    }

    public function setTeacherComment(?string $teacherComment): static
    {
        $this->teacherComment = $teacherComment;

        return $this;
    }
}

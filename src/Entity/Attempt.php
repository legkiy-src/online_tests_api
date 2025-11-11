<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Attempt\AttemptStatus;
use App\Repository\AttemptRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: AttemptRepository::class)]
class Attempt
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'attempts')]
    private Subject $subject;

    #[ORM\ManyToOne(targetEntity: Test::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Test $test;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(type: Types::INTEGER, precision: 5, scale: 2, nullable: true)]
    private ?int $score = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private int $maxScore;

    #[ORM\Column(type: Types::STRING, enumType: AttemptStatus::class)]
    private AttemptStatus $status;

    #[ORM\Column(type: 'integer')]
    private ?int $timeSpent = 0;

    #[ORM\OneToMany(targetEntity: AttemptAnswer::class, mappedBy: 'attempt', orphanRemoval: true)]
    private Collection $attemptAnswers;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $startedAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $completedAt = null;

    public function __construct(
        User $user,
        Subject $subject,
        Test $test,
        int $maxScore
    ) {
        $this->user = $user;
        $this->subject = $subject;
        $this->test = $test;
        $this->maxScore = $maxScore;
        $this->startedAt = new \DateTimeImmutable();
        $this->status = AttemptStatus::IN_PROGRESS;
        $this->attemptAnswers = new ArrayCollection();
    }

    public static function create(
        User $user,
        Subject $subject,
        Test $test,
        int $maxScore
    ): self
    {
        return new self(
            $user,
            $subject,
            $test,
            $maxScore
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getTest(): Test
    {
        return $this->test;
    }

    public function setTest(Test $test): static
    {
        $this->test = $test;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score === null ? null : $this->score / 100.0;
    }

    public function getScoreRaw(): ?int
    {
        return $this->score;
    }

    public function setScore(?float $score): static
    {
        $this->score = $score === null ? null : (int)round($score * 100);

        return $this;
    }

    public function setScoreRaw(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getMaxScore(): float
    {
        return $this->maxScore / 100.0;
    }

    public function getMaxScoreRaw(): int
    {
        return $this->maxScore;
    }

    public function setMaxScore(float $maxScore): static
    {
        $this->maxScore = (int)round($maxScore * 100);

        return $this;
    }

    public function setMaxScoreRaw(int $maxScore): static
    {
        $this->maxScore = $maxScore;

        return $this;
    }

    public function getStatus(): AttemptStatus
    {
        return $this->status;
    }

    public function setStatus(AttemptStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTimeSpend(): ?int
    {
        return $this->timeSpent;
    }

    public function setTimeSpend(?int $timeSpent): static
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }

    public function getAttemptAnswers(): Collection
    {
        return $this->attemptAnswers;
    }

    public function addAttemptAnswer(AttemptAnswer $attemptAnswer): static
    {
        if (!$this->attemptAnswers->contains($attemptAnswer)) {
            $this->attemptAnswers->add($attemptAnswer);
            $attemptAnswer->setAttempt($this);
        }

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Question\QuestionType;
use App\Repository\QuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'questions')]
    private Subject $subject;

    #[ORM\Column(type: Types::TEXT)]
    private string $questionText;

    #[ORM\Column(type: 'string', enumType: QuestionType::class)]
    private QuestionType $type;

    #[ORM\Column(type: 'integer')]
    private int $points = 1;

    #[ORM\Column(type: 'integer')]
    private int $orderIndex = 0;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        Subject $subject,
        string $questionText,
        QuestionType $type,
        int $points,
        int $orderIndex
    )
    {
        $this->subject = $subject;
        $this->questionText = $questionText;
        $this->type = $type;
        $this->points = $points;
        $this->orderIndex = $orderIndex;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function create(
        Subject $subject,
        string $questionText,
        QuestionType $type,
        int $points,
        int $orderIndex
    ): self {
        return new self(
            $subject,
            $questionText,
            $type,
            $points,
            $orderIndex
        );
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuestionText(): string
    {
        return $this->questionText;
    }

    public function setQuestionText(string $questionText): static
    {
        $this->questionText = $questionText;

        return $this;
    }

    public function getType(): QuestionType
    {
        return $this->type;
    }

    public function setType(QuestionType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getOrderIndex(): int
    {
        return $this->orderIndex;
    }

    public function setOrderIndex(int $orderIndex): static
    {
        $this->orderIndex = $orderIndex;

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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

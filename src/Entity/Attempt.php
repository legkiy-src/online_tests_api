<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Attempt\AttemptStatus;
use App\Repository\AttemptRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttemptRepository::class)]
class Attempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Subject::class, inversedBy: 'attempts')]
    private Subject $subject;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(type: 'string', enumType: AttemptStatus::class)]
    private AttemptStatus $status;

    #[ORM\Column(nullable: true)]
    private ?int $result = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $completedAt = null;

    public function __construct(
        Subject $subject,
        User $user,
        AttemptStatus $status
    ) {
        $this->subject = $subject;
        $this->user = $user;
        $this->status = $status;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(
        Subject $subject,
        User $user,
        AttemptStatus $status
    ): self {
        return new self(
            $subject,
            $user,
            $status
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

    public function getUser(): ?User
    {
        return $this->user;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
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

    public function getResult(): ?int
    {
        return $this->result;
    }

    public function setResult(?int $result): static
    {
        $this->result = $result;

        return $this;
    }
}

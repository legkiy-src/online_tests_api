<?php

declare(strict_types=1);

namespace App\ApiResource\Subject;

use Symfony\Component\Validator\Constraints as Assert;

class CreateSubjectDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'name не должен быть пустым')]
        #[Assert\Length(max: 255, maxMessage: 'максимальная длина name 255 символов')]
        private ?string $name,

        #[Assert\Length(max: 1000, maxMessage: 'максимальная длина name 1000 символов')]
        #[Assert\Length(max: 1000)]
        private ?string $description,

        private ?int $teacherId = null,

        private bool $isActive = false,

        private ?int $defaultTimeLimit = null
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTeacherId(): ?int
    {
        return $this->teacherId;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getDefaultTimeLimit(): ?int
    {
        return $this->defaultTimeLimit;
    }
}

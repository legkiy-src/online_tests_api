<?php

declare(strict_types=1);

namespace App\ApiResource\Subject;

class SubjectOutputDto
{
    public int $id;
    public string $name;
    public ?string $description;
    public bool $isActive;
    public ?int $timeLimit;
    public int $maxAttempts;
    public ?int $teacherId;
    public ?string $teacherName;
    public \DateTimeImmutable $createdAt;
    public ?\DateTimeImmutable $updatedAt;

    // Statistics
    public int $questionsCount;
    public int $activeQuestionsCount;
    public int $attemptsCount;
    public int $completedAttemptsCount;
    public ?float $averageScore;

    public function __construct(\App\Entity\Subject $subject)
    {
        $this->id = $subject->getId();
        $this->name = $subject->getName();
        $this->description = $subject->getDescription();
        $this->isActive = $subject->isActive();
        $this->timeLimit = $subject->getDefaultTimeLimit();
        //$this->maxAttempts = $subject->getMaxAttempts();
        $this->teacherId = $subject->getTeacher()?->getId();
        $this->teacherName = $subject->getTeacher()?->getFullName();
        $this->createdAt = $subject->getCreatedAt();
        $this->updatedAt = $subject->getUpdatedAt();

        // Calculate statistics
        $this->questionsCount = $subject->getQuestions()->count();
        $this->activeQuestionsCount = $subject->getQuestions()->filter(
            fn($q) => $q->isActive()
        )->count();
        $this->attemptsCount = $subject->getAttempts()->count();
        $this->completedAttemptsCount = $subject->getAttempts()->filter(
            fn($a) => $a->isCompleted()
        )->count();
        $this->averageScore = $this->calculateAverageScore($subject);
    }

    private function calculateAverageScore(\App\Entity\Subject $subject): ?float
    {
        $completedAttempts = $subject->getAttempts()->filter(
            fn($a) => $a->isCompleted() && $a->getScore() !== null
        );

        if ($completedAttempts->isEmpty()) {
            return null;
        }

        $totalScore = 0;
        foreach ($completedAttempts as $attempt) {
            $totalScore += $attempt->getScore();
        }

        return $totalScore / $completedAttempts->count();
    }
}

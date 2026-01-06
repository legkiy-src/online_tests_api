<?php

declare(strict_types=1);

namespace App\Service\Subject;

use App\ApiResource\Subject\CreateSubjectDto;
use App\ApiResource\Subject\SubjectOutputDto;
use App\Entity\Subject;
use App\Repository\SubjectRepository;

class CreateSubjectService
{
    public function __construct(private SubjectRepository $subjectRepository)
    {
    }

    public function __invoke(CreateSubjectDto $createSubjectDto): SubjectOutputDto
    {
        $subject = $this->createEntityFromDto($createSubjectDto);

        $this->subjectRepository->save($subject);

        return new SubjectOutputDto(
            id: $subject->getId(),
            name: $subject->getName(),
            createdAt: $subject->getCreatedAt(),
            updatedAt: $subject->getUpdatedAt(),
            description: $subject->getDescription(),
            teacherId: $subject->getTeacher()?->getId(),
            isActive: $subject->isActive(),
            defaultTimeLimit: $subject->getDefaultTimeLimit()
        );
    }

    private function createEntityFromDto(CreateSubjectDto $dto): Subject
    {
        return new Subject($dto->getName(), $dto->getDescription());
    }
}

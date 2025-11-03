<?php

declare(strict_types=1);

namespace App\State\Subject;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Subject\SubjectOutputDto;
use App\Repository\SubjectRepository;
use Symfony\Bundle\SecurityBundle\Security;

final class GetSubjectCollectionProvider implements ProviderInterface
{
    public function __construct(
        private SubjectRepository $subjectRepository,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        if ($user->getUserRole()->isTeacher()) {
            $subjects = $this->subjectRepository->findByTeacher($user);
        } else {
            $subjects = $this->subjectRepository->findActiveSubjects();
        }

        return array_map(
            fn($subject) => new SubjectOutputDto($subject),
            $subjects
        );
    }
}

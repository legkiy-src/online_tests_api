<?php

declare(strict_types=1);

namespace App\State\Subject;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
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
        return $this->subjectRepository->findAll();
    }
}

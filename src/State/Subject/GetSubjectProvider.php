<?php

declare(strict_types=1);

namespace App\State\Subject;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Subject\SubjectOutputDto;
use App\Entity\Subject;
use App\Repository\SubjectRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetSubjectProvider implements ProviderInterface
{
    public function __construct(
        private SubjectRepository $subjectRepository,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): SubjectOutputDto
    {
        $subject = $this->subjectRepository->find($uriVariables['id']);

        if (!$subject) {
            throw new NotFoundHttpException('Subject not found');
        }

        $this->checkAccess($subject);

        return new SubjectOutputDto($subject);
    }

    private function checkAccess(Subject $subject): void
    {
        $user = $this->security->getUser();

        if ($user->getUserRole()->isStudent() && !$subject->isActive()) {
            throw new AccessDeniedException('Subject is not active');
        }

        if ($user->getUserRole()->isTeacher() && $subject->getTeacher() !== $user) {
            throw new AccessDeniedException('Access denied to this subject');
        }
    }
}

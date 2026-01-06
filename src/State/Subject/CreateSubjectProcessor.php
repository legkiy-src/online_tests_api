<?php

declare(strict_types=1);

namespace App\State\Subject;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Subject\SubjectOutputDto;
use App\Service\Subject\CreateSubjectService;

class CreateSubjectProcessor implements ProcessorInterface
{
    public function __construct(private CreateSubjectService $createSubjectService)
    {
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): SubjectOutputDto
    {
        return $this->createSubjectService->__invoke($data);
    }
}

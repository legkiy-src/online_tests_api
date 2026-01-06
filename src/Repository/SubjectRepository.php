<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    public function save(Subject $subject, bool $flush = true): void
    {
        $this->getEntityManager()->persist($subject);

        if (true === $flush) {
            try {
                $this->getEntityManager()->flush();
            } catch (\Exception $exception) {
                throw new \RuntimeException('Ошибка сохранения Subject: ' . $exception->getMessage());
            }
        }
    }

    public function findAll(): array
    {
        return parent::findAll();
    }
}

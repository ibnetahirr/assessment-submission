<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\EntityRepository;

class AssessmentRepository extends EntityRepository
{
    public function findAssessmentById(string $id): ?Assessment
    {
        return $this->find($id);
    }

    public function findAssessmentInstanceById(string $id): ?AssessmentInstance
    {
        return $this->getEntityManager()
            ->getRepository(AssessmentInstance::class)
            ->find($id);
    }

    public function findAllAssessmentInstanceAnswers(AssessmentInstance $instance): array
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('assessmentAnswer')
            ->from(AssessmentAnswer::class, 'assessmentAnswer')
            ->andWhere('assessmentAnswer.assessmentInstance = :instance')
            ->setParameter('instance', $instance);

        return $qb->getQuery()->getResult();
    }

    public function findAssessmentAnswerOptionsByQuestion(AssessmentQuestion $question): array
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('assessmentAnswerOption')
            ->from(AssessmentAnswerOption::class, 'assessmentAnswerOption')
            ->andWhere('assessmentAnswerOption.assessmentQuestion = :question')
            ->setParameter('question', $question)
            ->addOrderBy('assessmentAnswerOption.optionNumber', 'ASC');

        return $qb->getQuery()->getResult();
    }
}

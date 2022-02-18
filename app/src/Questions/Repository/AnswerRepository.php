<?php
namespace App\Questions\Repository;

use App\Questions\Entity\Answer\Answer;
use App\Questions\Entity\Answer\AnswerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for Answer Entity
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * @param int $id ID ответа
     * @param bool $isPublished Опубликованный ответ?
     * @return Answer|null Найти ответ по ID
     */
    public function findOneById(int $id, bool $isPublished = true): ?Answer
    {
        $criteria = $isPublished ? ['status' => AnswerInterface::STATUS_PUBLISHED] : [];
        $criteria['id'] = $id;

        return $this->findOneBy($criteria);
    }
}

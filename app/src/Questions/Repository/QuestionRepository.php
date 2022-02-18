<?php
namespace App\Questions\Repository;

use App\Questions\Entity\Question\Question;
use App\Questions\Entity\Question\QuestionInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for Question Entity
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @param int $id ID вопроса
     * @param bool $isPublished Опубликованный вопрос?
     * @return Question|null Найти вопрос по ID
     */
    public function findOneById(int $id, bool $isPublished = true): ?Question
    {
        $criteria = $isPublished ? ['status' => QuestionInterface::STATUS_PUBLISHED] : [];
        $criteria['id'] = $id;

        return $this->findOneBy($criteria);
    }
}

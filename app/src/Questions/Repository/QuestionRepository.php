<?php
namespace App\Questions\Repository;

use App\Questions\Entity\Question\Question;
use App\Questions\Entity\Question\QuestionInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for Question Entity
 *
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
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

    /**
     * @param int $categoryId ID категории
     * @param string|null $status Статус вопросов
     * @return int Количество вопросов в указанной категории и статусе
     */
    public function countQuestionsByCategory(int $categoryId, ?string $status = null): int
    {
        $criteria = $status ? ['status' => $status] : [];
        $criteria['category'] = $categoryId;

        return $this->count($criteria);
    }
}

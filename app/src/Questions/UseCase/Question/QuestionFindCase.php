<?php
namespace App\Questions\UseCase\Question;

use App\Questions\Entity\Question\Question;
use App\Core\Exception\NotFoundEntityException;
use App\Questions\Repository\QuestionRepository;

/**
 * Question Case: Найти вопрос по различным критериям
 */
final class QuestionFindCase
{
    /**
     * @var QuestionRepository Question Repository
     */
    private QuestionRepository $questionRepository;

    /**
     * Конструктор сервиса
     *
     * @param QuestionRepository $questionRepository Question Repository
     *
     * @return void
     */
    public function __construct(
        QuestionRepository $questionRepository,
    )
    {
        $this->questionRepository = $questionRepository;
    }

    /**
     * @param int $id ID вопроса
     * @param bool $isPublished Выборка только опубликованных вопросов
     * @return Question Информация о вопросе
     * @throws NotFoundEntityException В случае если вопрос не найден
     */
    public function getQuestionById(int $id, bool $isPublished = true): Question
    {
        $question = $this->questionRepository->findOneById($id, $isPublished);
        if (empty($question)) {
            throw new NotFoundEntityException(sprintf("Вопрос с ID '%s' не найден.", $id));
        }

        return $question;
    }

    /**
     * @param int $categoryId ID категории
     * @param string|null $status Статус вопросов
     * @return int Количество вопросов в указанной категории и статусе
     */
    public function countQuestionsByCategory(int $categoryId, ?string $status = null): int
    {
        return $this->questionRepository->countQuestionsByCategory($categoryId, $status);
    }
}

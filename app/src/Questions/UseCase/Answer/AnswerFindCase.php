<?php
namespace App\Questions\UseCase\Answer;

use App\Questions\Entity\Answer\Answer;
use App\Core\Exception\NotFoundEntityException;
use App\Questions\Repository\AnswerRepository;

/**
 * Answer Case: Найти ответ по различным критериям
 */
final class AnswerFindCase
{
    /**
     * @var AnswerRepository Answer Repository
     */
    private AnswerRepository $answerRepository;

    /**
     * Конструктор сервиса
     *
     * @param AnswerRepository $answerRepository Answer Repository
     *
     * @return void
     */
    public function __construct(
        AnswerRepository $answerRepository,
    )
    {
        $this->answerRepository = $answerRepository;
    }

    /**
     * @param int $id ID ответа
     * @param bool $isPublished Выборка только опубликованных ответов
     * @return Answer Информация о ответе
     * @throws NotFoundEntityException В случае если ответ не найден
     */
    public function getAnswerById(int $id, bool $isPublished = true): Answer
    {
        $answer = $this->answerRepository->findOneById($id, $isPublished);
        if (empty($answer)) {
            throw new NotFoundEntityException(sprintf("Ответ с ID '%s' не найден.", $id));
        }

        return $answer;
    }

    /**
     * @param int $questionId ID вопроса
     * @param string|null $status Статус ответов
     * @return int Количество ответов в указанном вопросе и статусе
     */
    public function countAnswersByQuestion(int $questionId, ?string $status = null): int
    {
        return $this->answerRepository->countAnswersByQuestion($questionId, $status);
    }
}

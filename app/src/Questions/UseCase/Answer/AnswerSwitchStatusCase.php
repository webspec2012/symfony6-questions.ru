<?php
namespace App\Questions\UseCase\Answer;

use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Answer Case: Изменение статуса ответу
 */
final class AnswerSwitchStatusCase
{
    /**
     * @var AnswerFindCase Answer Find Case
     */
    private AnswerFindCase $answerFindCase;

    /**
     * @var EntityManagerInterface Entity Manager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var WorkflowInterface Workflow Interface
     */
    private WorkflowInterface $answerStatusWorkflow;

    /**
     * @var LoggerInterface Logger
     */
    private LoggerInterface $logger;

    /**
     * Конструктор сервиса
     *
     * @param AnswerFindCase $answerFindCase Answer Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param WorkflowInterface $questionsAnswerStatusStateMachine Workflow Interface
     * @param LoggerInterface $logger Logger
     *
     * @return void
     */
    public function __construct(
        AnswerFindCase $answerFindCase,
        EntityManagerInterface $entityManager,
        WorkflowInterface $questionsAnswerStatusStateMachine,
        LoggerInterface $logger,
    )
    {
        $this->answerFindCase = $answerFindCase;
        $this->entityManager = $entityManager;
        $this->answerStatusWorkflow = $questionsAnswerStatusStateMachine;
        $this->logger = $logger;
    }

    /**
     * Публикация ответа
     *
     * @param int $id ID ответа
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function publish(int $id): bool
    {
        return $this->answerStatusWorkflow($id, 'publish');
    }

    /**
     * Снятие с публикации ответа
     *
     * @param int $id ID ответа
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function unpublish(int $id): bool
    {
        return $this->answerStatusWorkflow($id, 'unpublish');
    }

    /**
     * Удаление ответа
     *
     * @param int $id ID ответа
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function delete(int $id): bool
    {
        return $this->answerStatusWorkflow($id, 'delete');
    }

    /**
     * Изменить статус ответу.
     * Проходит через систему workflow.
     *
     * @param int $id ID Ответа
     * @param string $action Действие
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    private function answerStatusWorkflow(int $id, string $action): bool
    {
        $answer = $this->answerFindCase->getAnswerById($id, false);

        if (!$this->answerStatusWorkflow->can($answer, $action)) {
            throw new ServiceException(sprintf("Действие '%s' недоступно для данного ответа. (workflow)", $action));
        }

        try {
            $this->answerStatusWorkflow->apply($answer, $action);
        } catch (\LogicException $e) {
            throw new ServiceException(sprintf("Произошла ошибка в процессе '%s'. Попробуйте позже.", $action));
        }

        try {
            $this->entityManager->persist($answer);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }
}

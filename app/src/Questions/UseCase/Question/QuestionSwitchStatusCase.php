<?php
namespace App\Questions\UseCase\Question;

use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Question Case: Изменение статуса вопросу
 */
final class QuestionSwitchStatusCase
{
    /**
     * @var QuestionFindCase Question Find Case
     */
    private QuestionFindCase $questionFindCase;

    /**
     * @var EntityManagerInterface Entity Manager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var WorkflowInterface Workflow Interface
     */
    private WorkflowInterface $questionStatusWorkflow;

    /**
     * @var LoggerInterface Logger
     */
    private LoggerInterface $logger;

    /**
     * Конструктор сервиса
     *
     * @param QuestionFindCase $questionFindCase Question Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param WorkflowInterface $questionsQuestionStatusStateMachine Workflow Interface
     * @param LoggerInterface $logger Logger
     *
     * @return void
     */
    public function __construct(
        QuestionFindCase $questionFindCase,
        EntityManagerInterface $entityManager,
        WorkflowInterface $questionsQuestionStatusStateMachine,
        LoggerInterface $logger,
    )
    {
        $this->questionFindCase = $questionFindCase;
        $this->entityManager = $entityManager;
        $this->questionStatusWorkflow = $questionsQuestionStatusStateMachine;
        $this->logger = $logger;
    }

    /**
     * Публикация вопроса
     *
     * @param int $id ID вопроса
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function publish(int $id): bool
    {
        return $this->questionStatusWorkflow($id, 'publish');
    }

    /**
     * Снятие с публикации вопроса
     *
     * @param int $id ID вопроса
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function unpublish(int $id): bool
    {
        return $this->questionStatusWorkflow($id, 'unpublish');
    }

    /**
     * Удаление вопроса
     *
     * @param int $id ID вопроса
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function delete(int $id): bool
    {
        return $this->questionStatusWorkflow($id, 'delete');
    }

    /**
     * Изменить статус вопросу.
     * Проходит через систему workflow.
     *
     * @param int $id ID Вопроса
     * @param string $action Действие
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    private function questionStatusWorkflow(int $id, string $action): bool
    {
        $question = $this->questionFindCase->getQuestionById($id, false);

        if (!$this->questionStatusWorkflow->can($question, $action)) {
            throw new ServiceException(sprintf("Действие '%s' недоступно для данного вопроса. (workflow)", $action));
        }

        try {
            $this->questionStatusWorkflow->apply($question, $action);
        } catch (\LogicException $e) {
            throw new ServiceException(sprintf("Произошла ошибка в процессе '%s'. Попробуйте позже.", $action));
        }

        try {
            $this->entityManager->persist($question);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }
}

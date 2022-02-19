<?php
namespace App\Users\UseCase\User;

use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * User Case: Изменение статуса пользователю
 */
final class UserSwitchStatusCase
{
    /**
     * @var UserFindCase User Find Case
     */
    private UserFindCase $userFindCase;

    /**
     * @var EntityManagerInterface Entity Manager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var WorkflowInterface Workflow Interface
     */
    private WorkflowInterface $userStatusWorkflow;

    /**
     * @var LoggerInterface Logger
     */
    private LoggerInterface $logger;

    /**
     * Конструктор сервиса
     *
     * @param UserFindCase $userFindCase User Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param WorkflowInterface $userStatusStateMachine Workflow Interface
     * @param LoggerInterface $logger Logger
     *
     * @return void
     */
    public function __construct(
        UserFindCase $userFindCase,
        EntityManagerInterface $entityManager,
        WorkflowInterface $userStatusStateMachine,
        LoggerInterface $logger,
    )
    {
        $this->userFindCase = $userFindCase;
        $this->entityManager = $entityManager;
        $this->userStatusWorkflow = $userStatusStateMachine;
        $this->logger = $logger;
    }

    /**
     * Блокировка пользователя
     *
     * @param int $id ID пользователя
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function block(int $id): bool
    {
        return $this->userStatusWorkflow($id, 'block');
    }

    /**
     * Удаление пользователя
     *
     * @param int $id ID пользователя
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function delete(int $id): bool
    {
        return $this->userStatusWorkflow($id, 'delete');
    }

    /**
     * Восстановление пользователя
     *
     * @param int $id ID пользователя
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    public function restore(int $id): bool
    {
        return $this->userStatusWorkflow($id, 'restore');
    }

    /**
     * Изменить статус пользователю.
     * Проходит через систему workflow.
     *
     * @param int $id ID пользователя
     * @param string $action Действие
     * @return bool Результат выполнения операции
     * @throws ServiceException|NotFoundEntityException
     */
    private function userStatusWorkflow(int $id, string $action): bool
    {
        $user = $this->userFindCase->getUserById($id, false);

        if (!$this->userStatusWorkflow->can($user, $action)) {
            throw new ServiceException(sprintf("Действие '%s' недоступно для данного пользователя. (workflow)", $action));
        }

        try {
            $this->userStatusWorkflow->apply($user, $action);
        } catch (\LogicException $e) {
            throw new ServiceException(sprintf("Произошла ошибка в процессе '%s'. Попробуйте позже.", $action));
        }

        try {
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());

            return false;
        }
    }
}

<?php
namespace App\Users\UseCase\User;

use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Users\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
     * Конструктор сервиса
     *
     * @param UserFindCase $userFindCase User Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param WorkflowInterface $userStatusStateMachine Workflow Interface
     *
     * @return void
     */
    public function __construct(
        UserFindCase $userFindCase,
        EntityManagerInterface $entityManager,
        WorkflowInterface $userStatusStateMachine
    )
    {
        $this->userFindCase = $userFindCase;
        $this->entityManager = $entityManager;
        $this->userStatusWorkflow = $userStatusStateMachine;
    }

    /**
     * Блокировка пользователя
     *
     * @param int $id ID пользователя
     * @return User Заблокированный пользователь
     * @throws ServiceException|NotFoundEntityException
     */
    public function block(int $id): User
    {
        return $this->userStatusWorkflow($id, 'block');
    }

    /**
     * Удаление пользователя
     *
     * @param int $id ID пользователя
     * @return User Удаленный пользователь
     * @throws ServiceException|NotFoundEntityException
     */
    public function delete(int $id): User
    {
        return $this->userStatusWorkflow($id, 'delete');
    }

    /**
     * Восстановление пользователя
     *
     * @param int $id ID пользователя
     * @return User Восстановенный пользователь
     * @throws ServiceException|NotFoundEntityException
     */
    public function restore(int $id): User
    {
        return $this->userStatusWorkflow($id, 'restore');
    }

    /**
     * Изменить статус пользователю.
     * Проходит через систему workflow.
     *
     * @param int $id ID пользователя
     * @param string $action Действие
     * @return User Обновленный пользователь
     * @throws ServiceException|NotFoundEntityException
     */
    private function userStatusWorkflow(int $id, string $action): User
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

        // save to DB
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}

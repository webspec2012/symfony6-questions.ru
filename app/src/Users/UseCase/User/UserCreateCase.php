<?php
namespace App\Users\UseCase\User;

use App\Users\Entity\User;
use App\Users\Entity\UserInterface;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Users\Dto\User\UserCreateForm;
use App\Users\Event\User\UserCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * User Case: Создание нового пользователя
 */
final class UserCreateCase
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
     * @var UserPasswordHasherInterface Password Encoder
     */
    private UserPasswordHasherInterface $passwordEncoder;

    /**
     * @var WorkflowInterface Workflow Interface
     */
    private WorkflowInterface $userStatusWorkflow;

    /**
     * @var EventDispatcherInterface Event Dispatcher
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * Конструктор сервиса
     *
     * @param UserFindCase $userFindCase User Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param UserPasswordHasherInterface $passwordEncoder Password Encoder
     * @param WorkflowInterface $userStatusStateMachine Workflow Interface
     * @param EventDispatcherInterface $eventDispatcher Event Dispatcher
     *
     * @return void
     */
    public function __construct(
        UserFindCase $userFindCase,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordEncoder,
        WorkflowInterface $userStatusStateMachine,
        EventDispatcherInterface $eventDispatcher,
    )
    {
        $this->userFindCase = $userFindCase;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userStatusWorkflow = $userStatusStateMachine;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Создание нового пользователя
     *
     * @param UserCreateForm $form DTO с данными пользователя
     * @return User Созданный пользователь
     * @throws ServiceException В случае ошибки
     */
    public function create(UserCreateForm $form): User
    {
        ValidateDtoService::validateDto($form);

        try {
            $this->userFindCase->getUserByEmail($form->email, false);
            throw new ServiceException(sprintf("E-mail адрес '%s' уже используется другим пользователем.", $form->email));
        } catch (NotFoundEntityException $e) {}

        $user = new User();
        $user->setUsername($form->name);
        $user->setEmail($form->email);
        $user->setPlainPassword($form->password, $this->passwordEncoder);
        $user->setIsAdmin($form->is_admin);
        $user->setRoles($form->roles);

        $user->setStatus(UserInterface::STATUS_ACTIVE);
        $this->userStatusWorkflow->getMarking($user);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(new UserCreatedEvent($user), UserCreatedEvent::NAME);
        } catch (\Throwable $e) {
            throw new ServiceException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return $this->userFindCase->getUserById($user->getId());
    }
}

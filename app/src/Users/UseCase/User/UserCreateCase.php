<?php
namespace App\Users\UseCase\User;

use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Users\Dto\User\UserCreateForm;
use App\Users\Entity\User;
use App\Users\Entity\UserInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Workflow\WorkflowInterface;

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
     * Конструктор сервиса
     *
     * @param UserFindCase $userFindCase User Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param UserPasswordHasherInterface $passwordEncoder Password Encoder
     * @param WorkflowInterface $userStatusStateMachine Workflow Interface
     *
     * @return void
     */
    public function __construct(
        UserFindCase $userFindCase,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordEncoder,
        WorkflowInterface $userStatusStateMachine
    )
    {
        $this->userFindCase = $userFindCase;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->userStatusWorkflow = $userStatusStateMachine;
    }

    /**
     * Создание нового пользователя
     *
     * @param UserCreateForm $form DTO с данными пользователя
     * @return User Созданный пользователь
     * @throws ServiceException В случае ошибки валидации данных
     * @throws ORMException В случае ошибки при сохранении в базу данных
     */
    public function create(UserCreateForm $form): User
    {
        ValidateDtoService::validateDto($form);

        try {
            $this->userFindCase->getUserByEmail($form->email, false);
            throw new ServiceException("Указанный E-mail адрес уже используется другим пользователем.");
        } catch (NotFoundEntityException $e) {}

        $user = new User();
        $user->setUsername($form->name);
        $user->setEmail($form->email);
        $user->setPlainPassword($form->password, $this->passwordEncoder);
        $user->setIsAdmin($form->is_admin);
        $user->setRoles($form->roles);

        // init status workflow
        $user->setStatus(UserInterface::STATUS_ACTIVE);
        $this->userStatusWorkflow->getMarking($user);

        // save to DB
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}

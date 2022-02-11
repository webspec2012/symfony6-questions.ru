<?php
namespace App\Users\UseCase\User;

use App\Core\Exception\EntityValidationException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Users\Dto\User\UserChangePasswordForm;
use App\Users\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * User Case: Изменение пароля пользователю
 */
final class UserChangePasswordCase
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
     * Конструктор сервиса
     *
     * @param UserFindCase $userFindCase User Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param UserPasswordHasherInterface $passwordEncoder Password Encoder
     *
     * @return void
     */
    public function __construct(
        UserFindCase $userFindCase,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordEncoder,
    )
    {
        $this->userFindCase = $userFindCase;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Изменение пароля пользователю
     *
     * @param UserChangePasswordForm $form DTO с данными пользователя
     * @return User Обновленный пользователь
     * @throws ServiceException|EntityValidationException|NotFoundEntityException
     */
    public function changePassword(UserChangePasswordForm $form): User
    {
        ValidateDtoService::validateDto($form);

        $user = $this->userFindCase->getUserById($form->id);
        $user->setPlainPassword($form->password, $this->passwordEncoder);

        // save to DB
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}

<?php
namespace App\Users\UseCase\User;

use App\Core\Exception\EntityValidationException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Users\Dto\User\UserUpdateForm;
use App\Users\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * User Case: Редактирование пользователя
 */
final class UserUpdateCase
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
     * Конструктор сервиса
     *
     * @param UserFindCase $userFindCase User Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     *
     * @return void
     */
    public function __construct(
        UserFindCase $userFindCase,
        EntityManagerInterface $entityManager
    )
    {
        $this->userFindCase = $userFindCase;
        $this->entityManager = $entityManager;
    }

    /**
     * Редактирование пользователя
     *
     * @param UserUpdateForm $form DTO с данными пользователя
     * @return User Обновленный пользователь
     * @throws ServiceException|EntityValidationException|NotFoundEntityException
     */
    public function update(UserUpdateForm $form): User
    {
        ValidateDtoService::validateDto($form);

        $user = $this->userFindCase->getUserById($form->id);

        $email = trim(mb_strtolower($form->email));
        if ($user->getEmail() !== $email) {
            try {
                $this->userFindCase->getUserByEmail($email, false);
                throw new ServiceException(sprintf("E-mail адрес '%s' уже используется другим пользователем.", $email));
            } catch (NotFoundEntityException $e) {}
        }

        $user->setUsername($form->name);
        $user->setEmail($form->email);
        $user->setIsAdmin($form->is_admin);
        $user->setRoles($form->roles);
        $user->setAbout((string) $form->about);

        // save to DB
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}

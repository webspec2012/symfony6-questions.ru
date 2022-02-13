<?php
namespace App\Users\UseCase\User;

use App\Core\Exception\EntityValidationException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Users\Dto\User\UserProfileUpdateForm;
use App\Users\Dto\User\UserUpdateForm;
use App\Users\Entity\User;
use App\Users\Event\User\UserEmailChangedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var EventDispatcherInterface Event Dispatcher
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * Конструктор сервиса
     *
     * @param UserFindCase $userFindCase User Find Case
     * @param EntityManagerInterface $entityManager Entity Manager
     * @param EventDispatcherInterface $eventDispatcher Event Dispatcher
     *
     * @return void
     */
    public function __construct(
        UserFindCase $userFindCase,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
    )
    {
        $this->userFindCase = $userFindCase;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
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
            } catch (NotFoundEntityException $e) {
                $user->setEmailVerified(false);
                $user->setEmailVerifiedToken(null);

                $emailChanged = true;
            }
        } else {
            $emailChanged = false;
        }

        $user->setUsername($form->name);
        $user->setEmail($form->email);
        $user->setIsAdmin($form->is_admin);
        $user->setRoles($form->roles);
        $user->setAbout((string) $form->about);

        // save to DB
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // user changed e-mail event
        if ($emailChanged) {
            $this->eventDispatcher->dispatch(new UserEmailChangedEvent($user), UserEmailChangedEvent::NAME);
        }

        return $user;
    }

    /**
     * Редактирование профиля пользователя
     *
     * @param UserProfileUpdateForm $form DTO с данными профиля пользователя
     * @return User Обновленный пользователь
     * @throws ServiceException|EntityValidationException|NotFoundEntityException
     */
    public function updateProfile(UserProfileUpdateForm $form): User
    {
        ValidateDtoService::validateDto($form);

        $user = $this->userFindCase->getUserById($form->id);

        $formData = new UserUpdateForm();
        $formData->id = $form->id;
        $formData->name = $form->name;
        $formData->email = $form->email;
        $formData->is_admin = $user->getIsAdmin();
        $formData->roles = $user->getRoles();
        $formData->about = $form->about;

        return $this->update($formData);
    }
}

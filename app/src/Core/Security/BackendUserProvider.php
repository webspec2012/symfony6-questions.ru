<?php
namespace App\Core\Security;

use App\Core\Exception\NotFoundEntityException;
use App\Users\Entity\User;
use App\Users\UseCase\User\UserFindCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * User Provider для backend приложения.
 */
final class BackendUserProvider implements UserProviderInterface
{
    /**
     * @var UserFindCase User Find Case
     */
    private UserFindCase $userFindCase;

    /**
     * Конструктор
     *
     * @param UserFindCase $userFindCase
     *
     * @return void
     */
    public function __construct
    (UserFindCase $userFindCase,
    )
    {
        $this->userFindCase = $userFindCase;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            $user = $this->userFindCase->getUserByEmail($identifier, true);
            if (!$user->isAdmin()) {
                throw new NotFoundEntityException("Доступ к данному разделу только с правами администратора.");
            }
        } catch (NotFoundEntityException $e) {
            throw new UserNotFoundException($e->getMessage());
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported. ', get_class($user))
            );
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}

<?php
namespace App\Users\Event\User;

use App\Users\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Событие: Пользователь изменил e-mail
 */
final class UserEmailChangedEvent extends Event
{
    /**
     * @const ID события
     */
    const NAME = 'user.email.changed';

    /**
     * @var UserInterface User
     */
    private UserInterface $user;

    /**
     * Конструктор
     *
     * @param UserInterface $user User
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface User
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}

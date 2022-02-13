<?php
namespace App\Users\Event\User;

use App\Users\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Событие связанное с пользователем
 */
abstract class BaseUserEvent extends Event
{
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

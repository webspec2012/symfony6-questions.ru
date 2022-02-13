<?php
namespace App\Users\Event\User;

/**
 * Событие: Создан новый пользователь
 */
final class UserCreatedEvent extends BaseUserEvent
{
    /**
     * @const ID события
     */
    public const NAME = 'user.created';
}

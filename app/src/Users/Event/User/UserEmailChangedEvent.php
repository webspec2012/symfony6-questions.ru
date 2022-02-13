<?php
namespace App\Users\Event\User;

/**
 * Событие: Пользователь изменил e-mail адрес
 */
final class UserEmailChangedEvent extends BaseUserEvent
{
    /**
     * @const ID события
     */
    public const NAME = 'user.email.changed';
}

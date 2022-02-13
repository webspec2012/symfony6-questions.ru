<?php
namespace App\Users\Message\User;

/**
 * Процесс подтверждения E-mail адреса пользователя.
 * Отправляет письмо с кодом подтверждения.
 */
class UserEmailVerification
{
    /**
     * @var int $userId ID пользователя
     */
    private int $userId;

    /**
     * Конструктор
     *
     * @param int $userId ID пользователя
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int User Id
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
}

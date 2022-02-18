<?php
namespace App\Users\Entity;

/**
 * Интерфейс для сущности "Пользователь"
 */
interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    /**
     * @const string Роль "Пользователь"
     */
    public const ROLE_USER = 'ROLE_USER';

    /**
     * @const string Роль "Администратор"
     */
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @const string Роль "Менеджер пользователей"
     */
    public const ROLE_MANAGER_USERS = 'ROLE_MANAGER_USERS';

    /**
     * @const string Роль "Менеджер вопросов-ответов"
     */
    public const ROLE_MANAGER_QUESTIONS = 'ROLE_MANAGER_QUESTIONS';

    /**
     * @const string Статус "Активен"
     */
    public const STATUS_ACTIVE = 'ACTIVE';

    /**
     * @const string Статус "Заблокирован"
     */
    public const STATUS_BLOCKED = 'BLOCKED';

    /**
     * @const string Статус "Удален"
     */
    public const STATUS_DELETED = 'DELETED';

    /**
     * @return int ID
     */
    public function getId(): int;

    /**
     * @return string Имя
     */
    public function getUsername(): string;

    /**
     * @return string E-mail
     */
    public function getEmail(): string;

    /**
     * @return bool E-mail подтверждён?
     */
    public function getEmailVerified(): bool;

    /**
     * @return bool E-mail подписан на рассылку?
     */
    public function getEmailSubscribed(): bool;

    /**
     * @return string Статус
     */
    public function getStatus(): string;

    /**
     * @return string[] Роли
     */
    public function getRoles(): array;

    /**
     * @return bool Активен?
     */
    public function isActive(): bool;

    /**
     * @return bool Заблокирован?
     */
    public function isBlocked(): bool;

    /**
     * @return bool Удалён?
     */
    public function isDeleted(): bool;

    /**
     * @return bool Администратор?
     */
    public function isAdmin(): bool;
}

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
    public const ROLE_USER = 'USER';

    /**
     * @const string Роль "Администратор"
     */
    public const ROLE_ADMIN = 'ADMIN';

    /**
     * @const string Роль "Менеджер пользователей"
     */
    public const ROLE_MANAGER_USERS = 'MANAGER_USERS';

    /**
     * @const string Роль "Менеджер вопросов-ответов"
     */
    public const ROLE_MANAGER_QUESTIONS = 'MANAGER_QUESTIONS';

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
     * @return int|null ID пользователя
     */
    public function getId(): ?int;

    /**
     * @return string Имя пользователя
     */
    public function getUsername(): string;

    /**
     * @return string E-mail пользователя
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
     * @return string Статус пользователя
     */
    public function getStatus(): string;

    /**
     * @return string[] Роли пользователя
     */
    public function getRoles(): array;

    /**
     * @return bool Пользователь активен?
     */
    public function isActive(): bool;

    /**
     * @return bool Пользователь администратор?
     */
    public function isAdmin(): bool;
}

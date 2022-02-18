<?php
namespace App\Users\Entity;

use App\Core\Entity\Traits\BlameableEntityTrait;
use App\Core\Entity\Traits\CreatedByIpEntityTrait;
use App\Core\Entity\Traits\StatusesEntityTrait;
use App\Core\Entity\Traits\TimestampableEntityTrait;
use App\Core\Exception\EntityValidationException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Сущность "Пользователь"
 *
 * @psalm-suppress MissingConstructor
 *
 * @ORM\Table(
 *     name="`user`",
 *     indexes={
 *          @ORM\Index(name="user_status", columns={"status"})
 *     }
 * )
 *
 * @ORM\Entity(
 *     repositoryClass="App\Users\Repository\UserRepository",
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use BlameableEntityTrait;
    use TimestampableEntityTrait;
    use StatusesEntityTrait;
    use CreatedByIpEntityTrait;

    /**
     * @var array Список статусов
     */
    public static array $statusList = [
        self::STATUS_ACTIVE => 'ACTIVE',
        self::STATUS_BLOCKED => 'BLOCKED',
        self::STATUS_DELETED => 'DELETED',
    ];

    /**
     * @var array Список ролей
     */
    public static array $rolesList = [
        self::ROLE_USER => 'ROLE_USER',
        self::ROLE_ADMIN => 'ROLE_ADMIN',
        self::ROLE_MANAGER_USERS => 'ROLE_MANAGER_USERS',
        self::ROLE_MANAGER_QUESTIONS => 'ROLE_MANAGER_QUESTIONS',
    ];

    /**
     * @var int ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(
     *     type="integer",
     *     nullable=false,
     * )
     */
    private int $id;

    /**
     * @var string Username
     *
     * @ORM\Column(
     *     type="string",
     *     length=200,
     *     nullable=false,
     * )
     */
    private string $username;

    /**
     * @var string E-mail
     *
     * @ORM\Column(
     *     type="string",
     *     length=180,
     *     nullable=false,
     *     unique=true,
     * )
     */
    private string $email;

    /**
     * @var bool E-mail подтверждён?
     *
     * @ORM\Column(
     *     type="boolean",
     *     nullable=false
     * )
     */
    private bool $email_verified = false;

    /**
     * @var string|null Token для подтверждения e-mail адреса
     *
     * @ORM\Column(
     *     type="string",
     *     length=100,
     *     unique=true,
     *     nullable=true
     * )
     */
    private ?string $email_verified_token = null;

    /**
     * @var bool E-mail подписан на рассылку?
     *
     * @ORM\Column(
     *     type="boolean",
     *     nullable=false
     * )
     */
    private bool $email_subscribed = false;

    /**
     * @var string|null Token для подписки на e-mail рассылку
     *
     * @ORM\Column(
     *     type="string",
     *     length=100,
     *     unique=true,
     *     nullable=true
     * )
     */
    private ?string $email_subscribed_token = null;

    /**
     * @var string|null Пароль (hash пароля)
     *
     * @ORM\Column(
     *     type="string"
     * )
     */
    private ?string $password = null;

    /**
     * @var string|null Token для восстановления пароля
     *
     * @ORM\Column(
     *     type="string",
     *     length=100,
     *     unique=true,
     *     nullable=true
     * )
     */
    private ?string $password_restore_token = null;

    /**
     * @var bool Пользователь является администратором?
     * Статус администратора предполагает доступ в панель администрирования.
     *
     * @ORM\Column(
     *     type="boolean",
     *     nullable=false
     * )
     */
    private bool $is_admin = false;

    /**
     * @var string[] Список ролей
     *
     * @ORM\Column(
     *     type="json"
     * )
     */
    private array $roles = [];

    /**
     * @var string О себе
     *
     * @ORM\Column(
     *     type="text",
     *     nullable=false
     * )
     */
    private string $about = '';

    /**
     * @return int ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string Username
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Установить Username
     *
     * @param string $username Username
     * @return static
     */
    public function setUsername(string $username): static
    {
        $this->username = trim(strip_tags($username));

        return $this;
    }

    /**
     * @return string E-mail
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Установить E-mail
     *
     * @param string $email E-mail
     * @return static
     */
    public function setEmail(string $email): static
    {
        $this->email = trim(mb_strtolower($email));

        return $this;
    }

    /**
     * @return bool E-mail подтверждён?
     */
    public function getEmailVerified(): bool
    {
        return $this->email_verified;
    }

    /**
     * Установить E-mail подтверждён?
     *
     * @param bool $email_verified E-mail Verified?
     * @return static
     */
    public function setEmailVerified(bool $email_verified): static
    {
        $this->email_verified = $email_verified;

        return $this;
    }

    /**
     * @return string|null Token для подтверждения e-mail адреса
     */
    public function getEmailVerifiedToken(): ?string
    {
        return $this->email_verified_token;
    }

    /**
     * Установить Token для подтверждения e-mail адреса
     *
     * @param string|null $email_verified_token E-mail Verified Token
     * @return static
     */
    public function setEmailVerifiedToken(?string $email_verified_token): static
    {
        $this->email_verified_token = $email_verified_token;

        return $this;
    }

    /**
     * @return bool E-mail подписан на рассылку?
     */
    public function getEmailSubscribed(): bool
    {
        return $this->email_subscribed;
    }

    /**
     * Установить E-mail подписан на рассылку?
     *
     * @param bool $email_subscribed E-mail Subscribed?
     * @return static
     */
    public function setEmailSubscribed(bool $email_subscribed): static
    {
        $this->email_subscribed = $email_subscribed;

        return $this;
    }

    /**
     * @return string|null Token для подписки на e-mail рассылку
     */
    public function getEmailSubscribedToken(): ?string
    {
        return $this->email_subscribed_token;
    }

    /**
     * Установить Token для подписки на e-mail рассылку
     *
     * @param string|null $email_subscribed_token E-mail Subscribed Token
     * @return static
     */
    public function setEmailSubscribedToken(?string $email_subscribed_token): static
    {
        $this->email_subscribed_token = $email_subscribed_token;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Установить пароль
     *
     * @param string $password Пароль
     * @return static
     */
    protected function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null Token для восстановления пароля
     */
    public function getPasswordRestoreToken(): ?string
    {
        return $this->password_restore_token;
    }

    /**
     * Установить Token для восстановления пароля
     *
     * @param string|null $password_restore_token Password Restore Token
     * @return static
     */
    public function setPasswordRestoreToken(?string $password_restore_token): static
    {
        $this->password_restore_token = $password_restore_token;

        return $this;
    }

    /**
     * @return bool Является администратором?
     */
    public function getIsAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Установить Является администратором?
     *
     * @param bool $is_admin Is Admin?
     * @return static
     */
    public function setIsAdmin(bool $is_admin): static
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    /**
     * @inheritdocACTIVE
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Установить список ролей
     *
     * @param string[] $roles Список ролей
     * @return static
     * @throws EntityValidationException
     */
    public function setRoles(array $roles): static
    {
        array_map(function (string $role) {
            if (!isset(static::getRolesList()[$role])) {
                throw new EntityValidationException(sprintf("Некорректная роль для пользователя: '%s'", $role));
            }
        }, $roles);

        if (!in_array(static::ROLE_USER, $roles)) {
            $roles[] = static::ROLE_USER;
        }

        $this->roles = $roles;

        return $this;
    }

    /**
     * @return string About
     */
    public function getAbout(): string
    {
        return $this->about;
    }

    /**
     * Установить About
     *
     * @param string $about About
     * @return static
     */
    public function setAbout(string $about): static
    {
        $this->about = trim(strip_tags($about));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getStatusList(): array
    {
        return self::$statusList;
    }

    /**
     * @return string[] Список возможных ролей пользователя
     */
    public static function getRolesList(): array
    {
        return self::$rolesList;
    }

    /**
     * @inheritdoc
     */
    public function isActive(): bool
    {
        return $this->getStatus() === self::STATUS_ACTIVE;
    }

    /**
     * @inheritdoc
     */
    public function isBlocked(): bool
    {
        return $this->getStatus() === self::STATUS_BLOCKED;
    }

    /**
     * @inheritdoc
     */
    public function isDeleted(): bool
    {
        return $this->getStatus() === self::STATUS_DELETED;
    }

    /**
     * @inheritdoc
     */
    public function isAdmin(): bool
    {
        return $this->getIsAdmin();
    }

    /**
     * @inheritdoc
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * Установить пароль пользователю
     *
     * @param string $password Пароль в открытом виде
     * @param UserPasswordHasherInterface $passwordEncoder Password Encoder
     * @return void
     * @throws EntityValidationException
     */
    public function setPlainPassword(string $password, UserPasswordHasherInterface $passwordEncoder) : void
    {
        $password = trim(strip_tags($password));
        if (mb_strlen($password) < 8) {
            throw new EntityValidationException("Пароль должен содержать не менее 8 символов.");
        }

        $this->setPassword($passwordEncoder->hashPassword($this, $password));
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}

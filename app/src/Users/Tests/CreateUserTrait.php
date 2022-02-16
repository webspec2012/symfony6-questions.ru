<?php
namespace App\Users\Tests;

use App\Core\Exception\ServiceException;
use App\Users\Dto\User\UserCreateForm;
use App\Users\Entity\User;
use App\Users\Entity\UserInterface;
use App\Users\UseCase\User\UserCreateCase;

/**
 * Trait для создания пользователя
 */
trait CreateUserTrait
{
    /**
     * Создаёт пользователя для тестирования
     *
     * @param string $email E-mail адрес пользователя
     * @param bool $isAdmin Администратор?
     * @return User Созданный пользователь
     * @throws ServiceException В случае ошибки
     */
    protected static function createUserWithEmail(
        string $email,
        bool $isAdmin = false
    ): User
    {
        /* @var UserCreateCase $userCreateCase */
        $userCreateCase = static::getAppContainer()->get(UserCreateCase::class);

        $formData = new UserCreateForm();
        $formData->name = ucfirst(explode('@', $email)[0]);
        $formData->email = $email;
        $formData->password = 'password_normal';

        if ($isAdmin) {
            $formData->is_admin = true;
            $formData->roles = [
                UserInterface::ROLE_ADMIN,
            ];
        } else {
            $formData->is_admin = false;
            $formData->roles = [
                UserInterface::ROLE_USER,
            ];
        }

        return $userCreateCase->create($formData);
    }
}

<?php
namespace App\Users\UseCase\User;

use App\Users\Dto\User\UserRegistrationForm;
use App\Users\Entity\User;
use App\Users\Entity\UserInterface;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Users\Dto\User\UserCreateForm;

/**
 * User Case: Регистрация пользователя
 */
final class UserRegistrationCase
{
    /**
     * @var UserCreateCase User Create Case
     */
    private UserCreateCase $userCreateCase;

    /**
     * Конструктор сервиса
     *
     * @param UserCreateCase $userCreateCase User Create Case
     *
     * @return void
     */
    public function __construct(
        UserCreateCase $userCreateCase,
    )
    {
        $this->userCreateCase = $userCreateCase;
    }

    /**
     * Регистрация нового пользователя (классический вариант)
     *
     * @param UserRegistrationForm $form DTO с данными пользователя
     * @return User Созданный пользователь
     * @throws ServiceException В случае ошибки валидации данных
     */
    public function registration(UserRegistrationForm $form): User
    {
        ValidateDtoService::validateDto($form);

        $formData = new UserCreateForm();
        $formData->name = $form->name;
        $formData->email = $form->email;
        $formData->password = $form->password;
        $formData->is_admin = false;
        $formData->roles = [
            UserInterface::ROLE_USER,
        ];

        return $this->userCreateCase->create($formData);
    }
}

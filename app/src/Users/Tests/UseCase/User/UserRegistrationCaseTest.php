<?php
namespace App\Users\Tests\UseCase\User;

use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Tests\Unit\BaseUnitTest;
use App\Users\Dto\User\UserRegistrationForm;
use App\Users\Entity\UserInterface;
use App\Users\UseCase\User\UserRegistrationCase;

/**
 * User Registration Case Test
 */
class UserRegistrationCaseTest extends BaseUnitTest
{
    /**
     * @var UserRegistrationCase|null User Registration Case
     */
    private static ?UserRegistrationCase $userRegistrationCase;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        static::$userRegistrationCase = static::getAppContainer()->get(UserRegistrationCase::class);
    }

    /**
     * @return void Регистрация нового пользователя (классический вариант), успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testRegistrationUserSuccess(): void
    {
        $formData = new UserRegistrationForm();
        $formData->name = ' <p>Registered <b>User</b> Success</p> ';
        $formData->email = 'registered-user-success@EXAMPLE.COM';
        $formData->password = 'registered-user-password';

        $user = static::$userRegistrationCase->registration($formData);
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertEquals('Registered User Success', $user->getUsername());
        $this->assertEquals('registered-user-success@example.com', $user->getEmail());
        $this->assertFalse($user->getIsAdmin());
        $this->assertEquals(json_encode([
            UserInterface::ROLE_USER,
        ]), json_encode($user->getRoles()));
    }

    /**
     * @return void Регистрация нового пользователя (классический вариант), ошибка валидации Name
     * @throws ServiceException В случае ошибки
     */
    public function testRegistrationUserWithFailedValidateName(): void
    {
        $formData = new UserRegistrationForm();
        $formData->name = '';
        $formData->email = 'registered-user-failed-name@example.com';
        $formData->password = 'registered-user-password';

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[name] This value should not be blank.',
            '[name] This value is too short. It should have 1 character or more.',
        ]));

        static::$userRegistrationCase->registration($formData);
    }

    /**
     * @return void Регистрация нового пользователя (классический вариант), ошибка валидации E-mail
     * @throws ServiceException В случае ошибки
     */
    public function testRegistrationUserWithFailedValidateEmail(): void
    {
        $formData = new UserRegistrationForm();
        $formData->name = 'Registered User Failed';
        $formData->email = '';
        $formData->password = 'registered-user-password';

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[email] This value should not be blank.',
            '[email] This value is too short. It should have 3 characters or more.',
        ]));

        static::$userRegistrationCase->registration($formData);
    }

    /**
     * @return void Создание нового пользователя, ошибка валидации Password
     * @throws ServiceException В случае ошибки
     */
    public function testRegistrationUserWithFailedValidatePassword(): void
    {
        $formData = new UserRegistrationForm();
        $formData->name = 'Registered User Failed';
        $formData->email = 'registered-user-failed-password@example.com';
        $formData->password = '';

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[password] This value should not be blank.',
            '[password] This value is too short. It should have 8 characters or more.',
        ]));

        static::$userRegistrationCase->registration($formData);
    }
}

<?php
namespace App\Users\Tests\UseCase\User;

use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Tests\Unit\BaseUnitTest;
use App\Users\Dto\User\UserChangePasswordForm;
use App\Users\Tests\CreateUserTrait;
use App\Users\Entity\UserInterface;
use App\Users\UseCase\User\UserChangePasswordCase;
use App\Users\UseCase\User\UserFindCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * User Change Password Case Test
 */
class UserChangePasswordCaseTest extends BaseUnitTest
{
    use CreateUserTrait;

    /**
     * @var UserFindCase|null $userFindCase User Find Case
     */
    static private ?UserFindCase $userFindCase;

    /**
     * @var UserChangePasswordCase|null $userChangePasswordCase User Change Password Case
     */
    static private ?UserChangePasswordCase $userChangePasswordCase;

    /**
     * @var UserPasswordHasherInterface|null $passwordEncoder Password Encoder
     */
    static private ?UserPasswordHasherInterface $passwordEncoder;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        static::$userFindCase = static::getAppContainer()->get(UserFindCase::class);
        static::$userChangePasswordCase = static::getAppContainer()->get(UserChangePasswordCase::class);
        static::$passwordEncoder = static::getAppContainer()->get(UserPasswordHasherInterface::class);
    }

    /**
     * @return void Изменение пароля пользователю, успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testChangePasswordSuccess(): void
    {
        $user = $this->createUserWithEmail('change-password-success@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);

        $formData = new UserChangePasswordForm();
        $formData->id = $user->getId();
        $formData->password = 'change_password_success';
        $this->assertTrue(static::$userChangePasswordCase->changePassword($formData));

        $userByEmail = static::$userFindCase->getUserByEmail($user->getEmail());
        $this->assertInstanceOf(UserInterface::class, $userByEmail);
        $this->assertTrue(static::$passwordEncoder->isPasswordValid($userByEmail, 'change_password_success'));
    }

    /**
     * @return void Изменение пароля пользователю, кейс с ошибкой валидации пароля
     * @throws ServiceException В случае ошибки
     */
    public function testChangePasswordWithFailedValidatePasswordLength(): void
    {
        $user = $this->createUserWithEmail('change-password-failed-validate-password-length@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);

        $formData = new UserChangePasswordForm();
        $formData->id = $user->getId();
        $formData->password = '123';

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[password] This value is too short. It should have 8 characters or more.'
        ]));

        static::$userChangePasswordCase->changePassword($formData);
    }

    /**
     * @return void Сформировать новый случайный пароль и отправить на почту пользователю, успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testGenerateNewPasswordAndSendToEmailSuccess(): void
    {
        $user = $this->createUserWithEmail('generate-new-password@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);
        $userPassword = $user->getPassword();

        $this->assertTrue(static::$userChangePasswordCase->generateNewPasswordAndSendToEmail($user->getId()));
        $this->assertNotEquals($userPassword, static::$userFindCase->getUserByEmail($user->getEmail())->getPassword());
    }
}

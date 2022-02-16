<?php
namespace App\Users\Tests\UseCase\User;

use App\Core\Exception\ServiceException;
use App\Tests\Unit\BaseUnitTest;
use App\Users\Tests\CreateUserTrait;
use App\Users\Entity\UserInterface;
use App\Users\UseCase\User\UserFindCase;
use App\Users\UseCase\User\UserPasswordRestoreCase;

/**
 * User Password Restore Case Test
 */
class UserPasswordRestoreCaseTest extends BaseUnitTest
{
    use CreateUserTrait;

    /**
     * @var UserFindCase|null $userFindCase User Find Case
     */
    static private ?UserFindCase $userFindCase;

    /**
     * @var UserPasswordRestoreCase|null $userPasswordRestoreCase
     */
    private static ?UserPasswordRestoreCase $userPasswordRestoreCase;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        static::$userFindCase = static::getAppContainer()->get(UserFindCase::class);
        static::$userPasswordRestoreCase = static::getAppContainer()->get(UserPasswordRestoreCase::class);
    }

    /**
     * @return void Отправка письма с токеном подтверждения, успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testPasswordRestoreSendEmailSuccess(): void
    {
        $user = $this->createUserWithEmail('password-restore-send-email-success@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);

        $this->assertTrue(static::$userPasswordRestoreCase->sendEmail($user->getId()));

        $userById = static::$userFindCase->getUserById($user->getId());
        $this->assertNotEmpty($userById->getPasswordRestoreToken());
        $this->assertStringContainsString('___', $userById->getPasswordRestoreToken());
    }

    /**
     * @return void Отправка письма с токеном подтверждения, кейс с ошибкой
     * @throws ServiceException В случае ошибки
     */
    public function testPasswordRestoreSendEmailWithFailedUserIsAdmin(): void
    {
        $admin = $this->createUserWithEmail('password-restore-send-email-failed-user-is-admin@example.com', true);
        $this->assertInstanceOf(UserInterface::class, $admin);

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Для данного пользователя функционал недоступен.");

        static::$userPasswordRestoreCase->sendEmail($admin->getId());
    }

    /**
     * @return void Процессинг восстановления пароля, успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testPasswordRestoreHandleSuccess(): void
    {
        $user = $this->createUserWithEmail('password-restore-handle-success@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue(static::$userPasswordRestoreCase->sendEmail($user->getId()));

        $userById = static::$userFindCase->getUserById($user->getId());
        $this->assertTrue(static::$userPasswordRestoreCase->handle($userById->getPasswordRestoreToken()));

        $userById = static::$userFindCase->getUserById($user->getId());
        $this->assertNull($userById->getPasswordRestoreToken());
    }

    /**
     * @return void Процессинг восстановления пароля, ошибка пустой токен
     * @throws ServiceException В случае ошибки
     */
    public function testPasswordRestoreHandleWithFailedTokenIsEmpty(): void
    {
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Не указан 'token' для восстановления пароля.");

        static::$userPasswordRestoreCase->handle('');
    }

    /**
     * @return void Процессинг восстановления пароля, ошибка указанный токен не найден
     * @throws ServiceException В случае ошибки
     */
    public function testPasswordRestoreHandleWithFailedTokenNotFound(): void
    {
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Указан невалидный token для восстановления пароля.");

        static::$userPasswordRestoreCase->handle('invalid_token');
    }
}

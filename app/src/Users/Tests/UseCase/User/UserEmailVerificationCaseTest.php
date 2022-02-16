<?php
namespace App\Users\Tests\UseCase\User;

use App\Core\Exception\ServiceException;
use App\Tests\Unit\BaseUnitTest;
use App\Users\Tests\CreateUserTrait;
use App\Users\Entity\UserInterface;
use App\Users\UseCase\User\UserEmailVerificationCase;
use App\Users\UseCase\User\UserFindCase;

/**
 * User Email Verification Case Test
 */
class UserEmailVerificationCaseTest extends BaseUnitTest
{
    use CreateUserTrait;

    /**
     * @var UserFindCase|null $userFindCase User Find Case
     */
    static private ?UserFindCase $userFindCase;

    /**
     * @var UserEmailVerificationCase|null $userEmailVerificationCase
     */
    private static ?UserEmailVerificationCase $userEmailVerificationCase;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        static::$userFindCase = static::getAppContainer()->get(UserFindCase::class);
        static::$userEmailVerificationCase = static::getAppContainer()->get(UserEmailVerificationCase::class);
    }

    /**
     * @return void Отправка письма с токеном подтверждения, успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testEmailVerificationSendEmailSuccess(): void
    {
        $user = $this->createUserWithEmail('email-verification-send-email-success@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);

        $oldToken = $user->getEmailVerifiedToken();
        $this->assertTrue(static::$userEmailVerificationCase->sendEmail($user->getId()));

        $userByEmail = static::$userFindCase->getUserByEmail($user->getEmail());
        $this->assertNotEquals($oldToken, $userByEmail->getEmailVerifiedToken());
        $this->assertStringContainsString('___', $userByEmail->getEmailVerifiedToken());
    }

    /**
     * @return void Отправка письма с токеном подтверждения, кейс с ошибкой
     * @throws ServiceException В случае ошибки
     */
    public function testEmailVerificationSendEmailWithFailedEmailAlreadyVerified(): void
    {
        $user = $this->createUserWithEmail('email-verification-send-email-already-verified@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);

        $this->assertTrue(static::$userEmailVerificationCase->handle($user->getEmailVerifiedToken()));

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(sprintf("У пользователя '%s' e-mail адрес уже подтверждён.", $user->getEmail()));

        static::$userEmailVerificationCase->sendEmail($user->getId());
    }

    /**
     * @return void Процессинг подтверждения E-mail адреса, успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testEmailVerificationHandleSuccess(): void
    {
        $user = $this->createUserWithEmail('email-verification-handle-success@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);

        $this->assertTrue(static::$userEmailVerificationCase->handle($user->getEmailVerifiedToken()));

        $userByEmail = static::$userFindCase->getUserByEmail($user->getEmail());
        $this->assertTrue($userByEmail->getEmailVerified());
        $this->assertNull($userByEmail->getEmailVerifiedToken());
    }

    /**
     * @return void Процессинг подтверждения E-mail адреса, ошибка пустой токен
     * @throws ServiceException В случае ошибки
     */
    public function testEmailVerificationHandleWithFailedTokenIsEmpty(): void
    {
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Не указан 'token' для подтверждения e-mail адреса");

        static::$userEmailVerificationCase->handle('');
    }

    /**
     * @return void Процессинг подтверждения E-mail адреса, ошибка указанный токен не найден
     * @throws ServiceException В случае ошибки
     */
    public function testEmailVerificationHandleWithFailedTokenNotFound(): void
    {
        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Указан невалидный token подтверждения e-mail адреса");

        static::$userEmailVerificationCase->handle('invalid_token');
    }
}

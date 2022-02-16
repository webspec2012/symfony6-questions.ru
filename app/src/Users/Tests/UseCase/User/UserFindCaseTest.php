<?php
namespace App\Users\Tests\UseCase\User;

use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Tests\Unit\BaseUnitTest;
use App\Users\Tests\CreateUserTrait;
use App\Users\Entity\UserInterface;
use App\Users\UseCase\User\UserFindCase;
use App\Users\UseCase\User\UserSwitchStatusCase;

/**
 * User Find Case Test
 */
class UserFindCaseTest extends BaseUnitTest
{
    use CreateUserTrait;

    /**
     * @var UserFindCase|null $userFindCase User Find Case
     */
    static private ?UserFindCase $userFindCase;

    /**
     * @var UserSwitchStatusCase|null $userSwitchStatusCase User Switch Status Case
     */
    static private ?UserSwitchStatusCase $userSwitchStatusCase;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        static::$userFindCase = static::getAppContainer()->get(UserFindCase::class);
        static::$userSwitchStatusCase = static::getAppContainer()->get(UserSwitchStatusCase::class);
    }

    /**
     * @return void Поиск пользователя по Id, статус ACTIVE, isActive is TRUE
     * @throws ServiceException|NotFoundEntityException В случае ошибки
     */
    public function testGetUserByIdWithStatusActiveAndIsActiveTrue(): void
    {
        $userEmail = 'get-by-id-status-active-is-active-true@example.com';
        $user = $this->createUserWithEmail($userEmail);
        $this->assertInstanceOf(UserInterface::class, $user);

        $userById = static::$userFindCase->getUserById($user->getId(), true);
        $this->assertInstanceOf(UserInterface::class, $userById);
        $this->assertEquals($userEmail, $userById->getEmail());
    }

    /**
     * @return void Поиск пользователя по Id, статус ACTIVE, isActive is FALSE
     * @throws ServiceException|NotFoundEntityException В случае ошибки
     */
    public function testGetUserByIdWithStatusActiveAndIsActiveFalse(): void
    {
        $userEmail = 'get-by-id-status-active-is-active-false@example.com';
        $user = $this->createUserWithEmail($userEmail);
        $this->assertInstanceOf(UserInterface::class, $user);

        $userById = static::$userFindCase->getUserById($user->getId(), false);
        $this->assertInstanceOf(UserInterface::class, $userById);
        $this->assertEquals($userEmail, $userById->getEmail());
    }

    /**
     * @return void Поиск пользователя по Id, статус BLOCKED, isActive is TRUE
     * @throws ServiceException В случае ошибки
     */
    public function testGetUserByIdWithStatusBlockedAndIsActiveTrue(): void
    {
        $user = $this->createUserWithEmail('get-by-id-status-blocked-is-active-true@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue(static::$userSwitchStatusCase->block($user->getId()));

        $this->expectException(NotFoundEntityException::class);
        $this->expectExceptionMessage(sprintf("Пользователь с ID '%s' не найден.", $user->getId()));

        static::$userFindCase->getUserById($user->getId(), true);
    }

    /**
     * @return void Поиск пользователя по Id, статус BLOCKED, isActive is FALSE
     * @throws ServiceException В случае ошибки
     */
    public function testGetUserByIdWithStatusBlockedAndIsActiveFalse(): void
    {
        $userEmail = 'get-by-id-status-blocked-is-active-false@example.com';
        $user = $this->createUserWithEmail($userEmail);
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue(static::$userSwitchStatusCase->block($user->getId()));

        $userById = static::$userFindCase->getUserById($user->getId(), false);
        $this->assertInstanceOf(UserInterface::class, $userById);
        $this->assertEquals($userEmail, $userById->getEmail());
    }

    /**
     * @return void Поиск пользователя по Id, статус DELETED, isActive is TRUE
     * @throws ServiceException В случае ошибки
     */
    public function testGetUserByIdWithStatusDeletedAndIsActiveTrue(): void
    {
        $user = $this->createUserWithEmail('get-by-id-status-deleted-is-active-true@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue(static::$userSwitchStatusCase->block($user->getId()));
        $this->assertTrue(static::$userSwitchStatusCase->delete($user->getId()));

        $this->expectException(NotFoundEntityException::class);
        $this->expectExceptionMessage(sprintf("Пользователь с ID '%s' не найден.", $user->getId()));

        static::$userFindCase->getUserById($user->getId(), true);
    }

    /**
     * @return void Поиск пользователя по Id, статус DELETED, isActive is FALSE
     * @throws ServiceException В случае ошибки
     */
    public function testGetUserByIdWithStatusDeletedAndIsActiveFalse(): void
    {
        $userEmail = 'get-by-id-status-deleted-is-active-false@example.com';
        $user = $this->createUserWithEmail($userEmail);
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue(static::$userSwitchStatusCase->block($user->getId()));
        $this->assertTrue(static::$userSwitchStatusCase->delete($user->getId()));

        $userById = static::$userFindCase->getUserById($user->getId(), false);
        $this->assertInstanceOf(UserInterface::class, $userById);
        $this->assertEquals($userEmail, $userById->getEmail());
    }

    /**
     * @return void Поиск пользователя по его Id, кейс с ошибкой (ID не найден)
     */
    public function testGetUserByIdWithFailedIdNotExists(): void
    {
        $randomUserId = rand(10000, 90000);

        $this->expectException(NotFoundEntityException::class);
        $this->expectExceptionMessage(sprintf("Пользователь с ID '%s' не найден.", $randomUserId));

        static::$userFindCase->getUserById($randomUserId);
    }

    /**
     * @return void Поиск пользователя по email, статус ACTIVE, isActive is TRUE
     * @throws ServiceException|NotFoundEntityException В случае ошибки
     */
    public function testGetUserByEmailWithStatusActiveAndIsActiveTrue(): void
    {
        $userEmail = 'get-by-email-status-active-is-active-true@example.com';
        $user = $this->createUserWithEmail($userEmail);
        $this->assertInstanceOf(UserInterface::class, $user);

        $userByEmail = static::$userFindCase->getUserByEmail($user->getEmail(), true);
        $this->assertInstanceOf(UserInterface::class, $userByEmail);
        $this->assertEquals($userEmail, $userByEmail->getEmail());
    }

    /**
     * @return void Поиск пользователя по email, статус ACTIVE, isActive is FALSE
     * @throws ServiceException|NotFoundEntityException В случае ошибки
     */
    public function testGetUserByEmailWithStatusActiveAndIsActiveFalse(): void
    {
        $userEmail = 'get-by-email-status-active-is-active-false@example.com';
        $user = $this->createUserWithEmail($userEmail);
        $this->assertInstanceOf(UserInterface::class, $user);

        $userByEmail = static::$userFindCase->getUserByEmail($user->getEmail(), false);
        $this->assertInstanceOf(UserInterface::class, $userByEmail);
        $this->assertEquals($userEmail, $userByEmail->getEmail());
    }

    /**
     * @return void Поиск пользователя по email, статус BLOCKED, isActive is TRUE
     * @throws ServiceException В случае ошибки
     */
    public function testGetUserByEmailWithStatusBlockedAndIsActiveTrue(): void
    {
        $user = $this->createUserWithEmail('get-by-email-status-blocked-is-active-true@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue(static::$userSwitchStatusCase->block($user->getId()));

        $this->expectException(NotFoundEntityException::class);
        $this->expectExceptionMessage(sprintf("Пользователь с E-mail '%s' не найден.", $user->getEmail()));

        static::$userFindCase->getUserByEmail($user->getEmail(), true);
    }

    /**
     * @return void Поиск пользователя по email, статус BLOCKED, isActive is FALSE
     * @throws ServiceException В случае ошибки
     */
    public function testGetUserByEmailWithStatusBlockedAndIsActiveFalse(): void
    {
        $userEmail = 'get-by-email-status-blocked-is-active-false@example.com';
        $user = $this->createUserWithEmail($userEmail);
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue(static::$userSwitchStatusCase->block($user->getId()));

        $userByEmail = static::$userFindCase->getUserByEmail($user->getEmail(), false);
        $this->assertInstanceOf(UserInterface::class, $userByEmail);
        $this->assertEquals($userEmail, $userByEmail->getEmail());
    }

    /**
     * @return void Поиск пользователя по email, статус DELETED, isActive is TRUE
     * @throws ServiceException В случае ошибки
     */
    public function testGetUserByEmailWithStatusDeletedAndIsActiveTrue(): void
    {
        $user = $this->createUserWithEmail('get-by-email-status-deleted-is-active-true@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue(static::$userSwitchStatusCase->block($user->getId()));
        $this->assertTrue(static::$userSwitchStatusCase->delete($user->getId()));

        $this->expectException(NotFoundEntityException::class);
        $this->expectExceptionMessage(sprintf("Пользователь с E-mail '%s' не найден.", $user->getEmail()));

        static::$userFindCase->getUserByEmail($user->getEmail(), true);
    }

    /**
     * @return void Поиск пользователя по email, статус DELETED, isActive is FALSE
     * @throws ServiceException В случае ошибки
     */
    public function testGetUserByEmailWithStatusDeletedAndIsActiveFalse(): void
    {
        $userEmail = 'get-by-email-status-deleted-is-active-false@example.com';
        $user = $this->createUserWithEmail($userEmail);
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertTrue(static::$userSwitchStatusCase->block($user->getId()));
        $this->assertTrue(static::$userSwitchStatusCase->delete($user->getId()));

        $userByEmail = static::$userFindCase->getUserByEmail($user->getEmail(), false);
        $this->assertInstanceOf(UserInterface::class, $userByEmail);
        $this->assertEquals($userEmail, $userByEmail->getEmail());
    }

    /**
     * @return void Поиск пользователя по его email, кейс с ошибкой (ID не найден)
     */
    public function testGetUserByEmailWithFailedEmailNotExists(): void
    {
        $randomUserEmail = sprintf("email-%s@example.com", rand(10000, 90000));

        $this->expectException(NotFoundEntityException::class);
        $this->expectExceptionMessage(sprintf("Пользователь с E-mail '%s' не найден.", $randomUserEmail));

        static::$userFindCase->getUserByEmail($randomUserEmail);
    }

    /**
     * @return void Поиск пользователя по Token для восстановления пароля, успешный кейс
     * @throws ServiceException|NotFoundEntityException В случае ошибки
     */
    public function testGetUserByEmailVerificationTokenSuccess(): void
    {
        $user = $this->createUserWithEmail('get-by-email-verification-token@example.com');
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertFalse($user->getEmailVerified());
        $this->assertNotEmpty($user->getEmailVerifiedToken());

        $userByEmailVerificationToken = static::$userFindCase->getUserByEmailVerificationToken($user->getEmailVerifiedToken());
        $this->assertEquals($user->getId(), $userByEmailVerificationToken->getId());
    }

    /**
     * @return void Поиск пользователя по Token для восстановления пароля, кейс с ошибкой
     */
    public function testGetUserByEmailVerificationTokenFailed(): void
    {
        $this->expectException(NotFoundEntityException::class);
        $this->expectExceptionMessage(sprintf("Пользователь с E-mail Verified Token '%s' не найден.", '1234567890'));

        static::$userFindCase->getUserByEmailVerificationToken(' <b>1234567890</b><br>');
    }

    /**
     * @return void Поиск пользователя по Token для восстановления пароля, кейс с ошибкой
     */
    public function testGetUserByPasswordRestoreTokenFailed(): void
    {
        $this->expectException(NotFoundEntityException::class);
        $this->expectExceptionMessage(sprintf("Пользователь с Password Reset Token '%s' не найден.", '1234567890'));

        static::$userFindCase->getUserByPasswordRestoreToken(' <b>1234567890</b><br>');
    }
}

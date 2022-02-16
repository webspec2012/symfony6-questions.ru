<?php
namespace App\Users\Tests\UseCase\User;

use App\Core\Exception\ServiceException;
use App\Tests\Unit\BaseUnitTest;
use App\Users\Tests\CreateUserTrait;
use App\Users\Entity\UserInterface;
use App\Users\UseCase\User\UserFindCase;
use App\Users\UseCase\User\UserSwitchStatusCase;

/**
 * User Switch Status Case Test
 */
class UserSwitchStatusCaseTest extends BaseUnitTest
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
     * @return void Переключение статуса (из статуса ACTIVE транзакция RESTORE)
     * @throws ServiceException В случае ошибки
     */
    public function testSwitchStatusFromActiveTransactionRestore(): void
    {
        $createdUser = $this->createUserWithEmail('switch-status-from-active-restore@example.com');
        $this->assertEquals(UserInterface::STATUS_ACTIVE, $createdUser->getStatus());

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Действие 'restore' недоступно для данного пользователя. (workflow)");

        static::$userSwitchStatusCase->restore($createdUser->getId());
    }

    /**
     * @return void Переключение статуса (из статуса ACTIVE транзакция BLOCK)
     * @throws ServiceException В случае ошибки
     */
    public function testSwitchStatusFromActiveTransactionBlock(): void
    {
        $createdUser = $this->createUserWithEmail('switch-status-from-active-block@example.com');
        $this->assertEquals(UserInterface::STATUS_ACTIVE, $createdUser->getStatus());

        $this->assertTrue(static::$userSwitchStatusCase->block($createdUser->getId()));

        $userByEmail = static::$userFindCase->getUserByEmail($createdUser->getEmail(), false);
        $this->assertEquals(UserInterface::STATUS_BLOCKED, $userByEmail->getStatus());
        $this->assertTrue($userByEmail->isBlocked());
    }

    /**
     * @return void Переключение статуса (из статуса ACTIVE транзакция DELETE)
     * @throws ServiceException В случае ошибки
     */
    public function testSwitchStatusFromActiveTransactionDelete(): void
    {
        $createdUser = $this->createUserWithEmail('switch-status-from-active-delete@example.com');
        $this->assertEquals(UserInterface::STATUS_ACTIVE, $createdUser->getStatus());

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Действие 'delete' недоступно для данного пользователя. (workflow)");

        static::$userSwitchStatusCase->delete($createdUser->getId());
    }

    /**
     * @return void Переключение статуса (из статуса BLOCKED транзакция RESTORE)
     * @throws ServiceException В случае ошибки
     */
    public function testSwitchStatusFromBlockedTransactionRestore(): void
    {
        $createdUser = $this->createUserWithEmail('switch-status-from-blocked-restore@example.com');
        $this->assertTrue(static::$userSwitchStatusCase->block($createdUser->getId()));
        $this->assertTrue(static::$userSwitchStatusCase->restore($createdUser->getId()));

        $userByEmail = static::$userFindCase->getUserByEmail($createdUser->getEmail(), false);
        $this->assertEquals(UserInterface::STATUS_ACTIVE, $userByEmail->getStatus());
        $this->assertTrue($userByEmail->isActive());
    }

    /**
     * @return void Переключение статуса (из статуса BLOCKED транзакция BLOCK)
     * @throws ServiceException В случае ошибки
     */
    public function testSwitchStatusFromBlockedTransactionBlock(): void
    {
        $createdUser = $this->createUserWithEmail('switch-status-from-blocked-block@example.com');
        $this->assertTrue(static::$userSwitchStatusCase->block($createdUser->getId()));

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Действие 'block' недоступно для данного пользователя. (workflow)");

        static::$userSwitchStatusCase->block($createdUser->getId());
    }

    /**
     * @return void Переключение статуса (из статуса BLOCKED транзакция DELETE)
     * @throws ServiceException В случае ошибки
     */
    public function testSwitchStatusFromBlockedTransactionDelete(): void
    {
        $createdUser = $this->createUserWithEmail('switch-status-from-blocked-delete@example.com');
        $this->assertTrue(static::$userSwitchStatusCase->block($createdUser->getId()));
        $this->assertTrue(static::$userSwitchStatusCase->delete($createdUser->getId()));

        $userByEmail = static::$userFindCase->getUserByEmail($createdUser->getEmail(), false);
        $this->assertEquals(UserInterface::STATUS_DELETED, $userByEmail->getStatus());
        $this->assertTrue($userByEmail->isDeleted());
    }

    /**
     * @return void Переключение статуса (из статуса DELETED транзакция RESTORE)
     * @throws ServiceException В случае ошибки
     */
    public function testSwitchStatusFromDeletedTransactionRestore(): void
    {
        $createdUser = $this->createUserWithEmail('switch-status-from-deleted-restore@example.com');
        $this->assertTrue(static::$userSwitchStatusCase->block($createdUser->getId()));
        $this->assertTrue(static::$userSwitchStatusCase->delete($createdUser->getId()));
        $this->assertTrue(static::$userSwitchStatusCase->restore($createdUser->getId()));

        $userByEmail = static::$userFindCase->getUserByEmail($createdUser->getEmail());
        $this->assertEquals(UserInterface::STATUS_ACTIVE, $userByEmail->getStatus());
        $this->assertTrue($userByEmail->isActive());
    }

    /**
     * @return void Переключение статуса (из статуса DELETED транзакция BLOCK)
     * @throws ServiceException В случае ошибки
     */
    public function testSwitchStatusFromDeletedTransactionBlock(): void
    {
        $createdUser = $this->createUserWithEmail('switch-status-from-deleted-block@example.com');
        $this->assertTrue(static::$userSwitchStatusCase->block($createdUser->getId()));
        $this->assertTrue(static::$userSwitchStatusCase->delete($createdUser->getId()));

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Действие 'block' недоступно для данного пользователя. (workflow)");

        static::$userSwitchStatusCase->block($createdUser->getId());
    }

    /**
     * @return void Переключение статуса (из статуса DELETED транзакция DELETE)
     * @throws ServiceException В случае ошибки
     */
    public function testSwitchStatusFromDeletedTransactionDelete(): void
    {
        $createdUser = $this->createUserWithEmail('switch-status-from-deleted-delete@example.com');
        $this->assertTrue(static::$userSwitchStatusCase->block($createdUser->getId()));
        $this->assertTrue(static::$userSwitchStatusCase->delete($createdUser->getId()));

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage("Действие 'delete' недоступно для данного пользователя. (workflow)");

        static::$userSwitchStatusCase->delete($createdUser->getId());
    }
}

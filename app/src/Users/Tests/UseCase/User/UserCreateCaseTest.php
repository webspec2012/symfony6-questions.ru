<?php
namespace App\Users\Tests\UseCase\User;

use App\Core\Exception\EntityValidationException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Tests\Unit\BaseUnitTest;
use App\Users\Dto\User\UserCreateForm;
use App\Users\Entity\UserInterface;
use App\Users\Tests\CreateUserTrait;
use App\Users\UseCase\User\UserCreateCase;
use App\Users\UseCase\User\UserFindCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * User Create Case Test
 */
class UserCreateCaseTest extends BaseUnitTest
{
    use CreateUserTrait;

    /**
     * @var UserFindCase|null $userFindCase User Find Case
     */
    static private ?UserFindCase $userFindCase;

    /**
     * @var UserCreateCase|null User Create Case
     */
    private static ?UserCreateCase $userCreateCase;

    /**
     * @var UserPasswordHasherInterface|null Password Encoder
     */
    private static ?UserPasswordHasherInterface $passwordEncoder;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        static::$userFindCase = static::getAppContainer()->get(UserFindCase::class);
        static::$userCreateCase = static::getAppContainer()->get(UserCreateCase::class);
        static::$passwordEncoder = static::getAppContainer()->get(UserPasswordHasherInterface::class);
    }

    /**
     * @return void Создание нового пользователя, успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testCreateUserSuccess(): void
    {
        $formData = new UserCreateForm();
        $formData->name = ' <p>Created <b>User</b> Success</p> ';
        $formData->email = 'created-user-success@EXAMPLE.COM';
        $formData->password = 'created-user-password';
        $formData->is_admin = false;
        $formData->roles = [
            UserInterface::ROLE_USER,
        ];

        $user = static::$userCreateCase->create($formData);
        $this->assertInstanceOf(UserInterface::class, $user);

        // id
        $this->assertThat($user->getId(), $this->logicalAnd(
            $this->isType('int'),
            $this->greaterThan(0)
        ));

        // username
        $this->assertEquals('Created User Success', $user->getUsername());

        // email
        $this->assertEquals('created-user-success@example.com', $user->getEmail());
        $this->assertFalse($user->getEmailVerified());
        $this->assertNotEmpty($user->getEmailVerifiedToken());
        $this->assertFalse($user->getEmailSubscribed());
        $this->assertEmpty($user->getEmailSubscribedToken());

        // password
        $this->assertNotEmpty($user->getPassword());
        $this->assertEmpty($user->getPasswordRestoreToken());
        $this->assertTrue(static::$passwordEncoder->isPasswordValid($user, 'created-user-password'));

        // is_admin
        $this->assertFalse($user->getIsAdmin());

        // roles
        $this->assertEquals(json_encode([
            UserInterface::ROLE_USER,
        ]), json_encode($user->getRoles()));

        // status
        $this->assertEquals(UserInterface::STATUS_ACTIVE, $user->getStatus());

        // created_at & updated_at
        $this->assertTrue($user->getCreatedAt() instanceof \DateTime);
        $this->assertTrue($user->getUpdatedAt() instanceof \DateTime);

        // created_by && updated_by
        $this->assertNull($user->getCreatedBy());
        $this->assertNull($user->getUpdatedBy());

        // other
        $this->assertEquals($user->getEmail(), $user->getUserIdentifier());
        $this->assertTrue($user->isActive());
        $this->assertFalse($user->isBlocked());
        $this->assertFalse($user->isDeleted());
        $this->assertFalse($user->isAdmin());
    }

    /**
     * @return void Создание нового администратора, успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testCreateAdminSuccess(): void
    {
        $formData = new UserCreateForm();
        $formData->name = 'Created Admin Success';
        $formData->email = 'created-admin-success@example.com';
        $formData->password = 'created-admin-password';
        $formData->is_admin = true;
        $formData->roles = [
            UserInterface::ROLE_ADMIN,
        ];

        $user = static::$userCreateCase->create($formData);
        $this->assertInstanceOf(UserInterface::class, $user);

        // id
        $this->assertThat($user->getId(), $this->logicalAnd(
            $this->isType('int'),
            $this->greaterThan(0)
        ));

        // username
        $this->assertEquals('Created Admin Success', $user->getUsername());

        // email
        $this->assertEquals('created-admin-success@example.com', $user->getEmail());
        $this->assertFalse($user->getEmailVerified());
        //$this->assertNotEmpty($user->getEmailVerifiedToken());
        $this->assertFalse($user->getEmailSubscribed());
        $this->assertEmpty($user->getEmailSubscribedToken());

        // password
        $this->assertNotEmpty($user->getPassword());
        $this->assertEmpty($user->getPasswordRestoreToken());
        $this->assertTrue(static::$passwordEncoder->isPasswordValid($user, 'created-admin-password'));

        // is_admin
        $this->assertTrue($user->getIsAdmin());

        // roles
        $this->assertEquals(json_encode([
            UserInterface::ROLE_ADMIN,
            UserInterface::ROLE_USER,
        ]), json_encode($user->getRoles()));

        // status
        $this->assertEquals(UserInterface::STATUS_ACTIVE, $user->getStatus());

        // created_at & updated_at
        $this->assertTrue($user->getCreatedAt() instanceof \DateTime);
        $this->assertTrue($user->getUpdatedAt() instanceof \DateTime);

        // created_by && updated_by
        $this->assertNull($user->getCreatedBy());
        $this->assertNull($user->getUpdatedBy());

        // other
        $this->assertEquals($user->getEmail(), $user->getUserIdentifier());
        $this->assertTrue($user->isActive());
        $this->assertFalse($user->isBlocked());
        $this->assertFalse($user->isDeleted());
        $this->assertTrue($user->isAdmin());
    }

    /**
     * @return void Создание нового пользователя, ошибка валидации Name
     * @throws ServiceException В случае ошибки
     */
    public function testCreateUserWithFailedValidateName(): void
    {
        $formData = new UserCreateForm();
        $formData->name = '';
        $formData->email = 'created-user-failed-name@example.com';
        $formData->password = 'created-user-password';
        $formData->is_admin = false;
        $formData->roles = [
            UserInterface::ROLE_USER,
        ];

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[name] This value should not be blank.',
            '[name] This value is too short. It should have 1 character or more.',
        ]));

        static::$userCreateCase->create($formData);
    }

    /**
     * @return void Создание нового пользователя, ошибка валидации E-mail
     * @throws ServiceException В случае ошибки
     */
    public function testCreateUserWithFailedValidateEmail(): void
    {
        $formData = new UserCreateForm();
        $formData->name = 'Created User Failed';
        $formData->email = '';
        $formData->password = 'created-user-password';
        $formData->is_admin = false;
        $formData->roles = [
            UserInterface::ROLE_USER,
        ];

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[email] This value should not be blank.',
            '[email] This value is too short. It should have 3 characters or more.',
        ]));

        static::$userCreateCase->create($formData);
    }

    /**
     * @return void Создание нового пользователя, ошибка валидации E-mail (уже используется)
     * @throws ServiceException В случае ошибки
     */
    public function testCreateUserWithFailedValidateEmailAlreadyUsed(): void
    {
        $alreadyCreatedUser = $this->createUserWithEmail('already-created-user@example.com');

        $formData = new UserCreateForm();
        $formData->name = 'Created User Failed';
        $formData->email = mb_strtoupper($alreadyCreatedUser->getEmail());
        $formData->password = 'created-user-password';
        $formData->is_admin = false;
        $formData->roles = [
            UserInterface::ROLE_USER,
        ];

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(sprintf("E-mail адрес '%s' уже используется другим пользователем.", $formData->email));

        static::$userCreateCase->create($formData);
    }

    /**
     * @return void Создание нового пользователя, ошибка валидации Password
     * @throws ServiceException В случае ошибки
     */
    public function testCreateUserWithFailedValidatePassword(): void
    {
        $formData = new UserCreateForm();
        $formData->name = 'Created User Failed';
        $formData->email = 'created-user-failed-password@example.com';
        $formData->password = '';
        $formData->is_admin = false;
        $formData->roles = [
            UserInterface::ROLE_USER,
        ];

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[password] This value should not be blank.',
            '[password] This value is too short. It should have 8 characters or more.',
        ]));

        static::$userCreateCase->create($formData);
    }

    /**
     * @return void Создание нового пользователя, ошибка валидации roles (некорректная роль)
     * @throws ServiceException В случае ошибки
     */
    public function testCreateUserWithFailedValidateRolesEmpty(): void
    {
        $formData = new UserCreateForm();
        $formData->name = 'Created User Failed';
        $formData->email = 'created-user-failed-roles@example.com';
        $formData->password = 'created-user-password';
        $formData->is_admin = false;

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[roles] This value should not be blank.',
        ]));

        static::$userCreateCase->create($formData);
    }

    /**
     * @return void Создание нового пользователя, ошибка валидации roles (некорректная роль)
     * @throws ServiceException В случае ошибки
     */
    public function testCreateUserWithFailedValidateRolesInvalidRole(): void
    {
        $formData = new UserCreateForm();
        $formData->name = 'Created User Failed';
        $formData->email = 'created-user-failed-roles2@example.com';
        $formData->password = 'created-user-password';
        $formData->is_admin = false;
        $formData->roles = [
            'ROLE_TESTMAN',
        ];

        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage("Некорректная роль для пользователя: 'ROLE_TESTMAN'");

        static::$userCreateCase->create($formData);
    }
}

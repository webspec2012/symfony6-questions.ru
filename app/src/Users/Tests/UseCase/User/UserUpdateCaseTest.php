<?php
namespace App\Users\Tests\UseCase\User;

use App\Core\Exception\EntityValidationException;
use App\Core\Exception\ServiceException;
use App\Core\Service\ValidateDtoService;
use App\Tests\Unit\BaseUnitTest;
use App\Users\Dto\User\UserProfileUpdateForm;
use App\Users\Dto\User\UserUpdateForm;
use App\Users\Entity\UserInterface;
use App\Users\Tests\CreateUserTrait;
use App\Users\UseCase\User\UserFindCase;
use App\Users\UseCase\User\UserUpdateCase;

/**
 * User Update Case Test
 */
class UserUpdateCaseTest extends BaseUnitTest
{
    use CreateUserTrait;

    /**
     * @var UserFindCase|null $userFindCase User Find Case
     */
    static private ?UserFindCase $userFindCase;

    /**
     * @var UserUpdateCase|null User Update Case
     */
    private static ?UserUpdateCase $userUpdateCase;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        static::$userFindCase = static::getAppContainer()->get(UserFindCase::class);
        static::$userUpdateCase = static::getAppContainer()->get(UserUpdateCase::class);
    }

    /**
     * @return void Редактирование пользователя, успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateUserSuccess(): void
    {
        $formData = new UserUpdateForm($this->createUserWithEmail('update-user-success@example.com'));
        $formData->name = '<b>Ivan Katkov</b>';
        $formData->email = 'update-user-success2@example.com';
        $formData->is_admin = true;
        $formData->roles = [
            UserInterface::ROLE_ADMIN,
        ];
        $formData->about = '<p>About Text</p>';

        $this->assertTrue(static::$userUpdateCase->update($formData));

        $updatedUser = static::$userFindCase->getUserById($formData->id);
        $this->assertInstanceOf(UserInterface::class, $updatedUser);

        $this->assertEquals('Ivan Katkov', $updatedUser->getUsername());
        $this->assertEquals('update-user-success2@example.com', $updatedUser->getEmail());
        $this->assertEquals('About Text', $updatedUser->getAbout());
        $this->assertFalse($updatedUser->getEmailVerified());
        $this->assertNotEmpty($updatedUser->getEmailVerifiedToken());
        $this->assertFalse($updatedUser->getEmailSubscribed());
        $this->assertEmpty($updatedUser->getEmailSubscribedToken());
        $this->assertTrue($updatedUser->getIsAdmin());
        $this->assertEquals(json_encode([
            UserInterface::ROLE_ADMIN,
            UserInterface::ROLE_USER,
        ]), json_encode($updatedUser->getRoles()));
        $this->assertEquals(UserInterface::STATUS_ACTIVE, $updatedUser->getStatus());
        $this->assertTrue($updatedUser->getCreatedAt() instanceof \DateTime);
        $this->assertTrue($updatedUser->getUpdatedAt() instanceof \DateTime);
        $this->assertNull($updatedUser->getCreatedBy());
        $this->assertNull($updatedUser->getUpdatedBy());
        $this->assertTrue($updatedUser->isAdmin());
    }

    /**
     * @return void Редактирование пользователя, ошибка валидации Id
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateUserWithFailedValidateId(): void
    {
        $formData = new UserUpdateForm($this->createUserWithEmail('update-user-failed-validate-id@example.com'));
        $formData->id = 0;

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(sprintf("Пользователь с ID '%s' не найден.", $formData->id));

        $this->assertTrue(static::$userUpdateCase->update($formData));
    }

    /**
     * @return void Редактирование пользователя, ошибка валидации Name
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateUserWithFailedValidateName(): void
    {
        $formData = new UserUpdateForm($this->createUserWithEmail('update-user-failed-validate-name@example.com'));
        $formData->name = '';

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[name] This value should not be blank.',
            '[name] This value is too short. It should have 1 character or more.',
        ]));

        $this->assertTrue(static::$userUpdateCase->update($formData));
    }

    /**
     * @return void Редактирование пользователя, ошибка валидации E-mail
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateUserWithFailedValidateEmail(): void
    {
        $formData = new UserUpdateForm($this->createUserWithEmail('update-user-failed-validate-email@example.com'));
        $formData->email = '';

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[email] This value should not be blank.',
            '[email] This value is too short. It should have 3 characters or more.',
        ]));

        $this->assertTrue(static::$userUpdateCase->update($formData));
    }

    /**
     * @return void Редактирование пользователя, ошибка валидации E-mail (уже используется)
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateUserWithFailedValidateEmailAlreadyUsed(): void
    {
        $alreadyCreatedUser = $this->createUserWithEmail('user-update-already-created-user@example.com');

        $formData = new UserUpdateForm($this->createUserWithEmail('update-user-failed-validate-email-already-used@example.com'));
        $formData->email = $alreadyCreatedUser->getEmail();

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(sprintf("E-mail адрес '%s' уже используется другим пользователем.", $formData->email));

        $this->assertTrue(static::$userUpdateCase->update($formData));
    }

    /**
     * @return void Редактирование пользователя, ошибка валидации roles (некорректная роль)
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateUserWithFailedValidateRolesEmpty(): void
    {
        $formData = new UserUpdateForm($this->createUserWithEmail('update-user-failed-validate-roles-empty@example.com'));
        $formData->roles = [];

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[roles] This value should not be blank.',
        ]));

        $this->assertTrue(static::$userUpdateCase->update($formData));
    }

    /**
     * @return void Редактирование пользователя, ошибка валидации roles (некорректная роль)
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateUserWithFailedValidateRolesInvalidRole(): void
    {
        $formData = new UserUpdateForm($this->createUserWithEmail('update-user-failed-validate-roles-invalid-role@example.com'));
        $formData->roles = [
            'ROLE_TESTMAN',
        ];

        $this->expectException(EntityValidationException::class);
        $this->expectExceptionMessage("Некорректная роль для пользователя: 'ROLE_TESTMAN'");

        $this->assertTrue(static::$userUpdateCase->update($formData));
    }

    /**
     * @return void Редактирование профиля, успешный кейс
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateProfileSuccess(): void
    {
        $formData = new UserProfileUpdateForm($this->createUserWithEmail('update-profile-success@example.com'));
        $formData->name = '<b>Ivan Katkov</b>';
        $formData->email = 'update-profile-success2@example.com';
        $formData->about = '<p>About Text</p>';

        $this->assertTrue(static::$userUpdateCase->updateProfile($formData));

        $updatedUser = static::$userFindCase->getUserById($formData->id);
        $this->assertInstanceOf(UserInterface::class, $updatedUser);

        $this->assertEquals('Ivan Katkov', $updatedUser->getUsername());
        $this->assertEquals('update-profile-success2@example.com', $updatedUser->getEmail());
        $this->assertEquals('About Text', $updatedUser->getAbout());
        $this->assertFalse($updatedUser->getEmailVerified());
        //$this->assertNotEmpty($updatedUser->getEmailVerifiedToken());
        $this->assertFalse($updatedUser->getEmailSubscribed());
        $this->assertEmpty($updatedUser->getEmailSubscribedToken());
        $this->assertFalse($updatedUser->getIsAdmin());
        $this->assertEquals(json_encode([
            UserInterface::ROLE_USER,
        ]), json_encode($updatedUser->getRoles()));
        $this->assertEquals(UserInterface::STATUS_ACTIVE, $updatedUser->getStatus());
        $this->assertTrue($updatedUser->getCreatedAt() instanceof \DateTime);
        $this->assertTrue($updatedUser->getUpdatedAt() instanceof \DateTime);
        $this->assertNull($updatedUser->getCreatedBy());
        $this->assertNull($updatedUser->getUpdatedBy());
        $this->assertFalse($updatedUser->isAdmin());
    }

    /**
     * @return void Редактирование профиля, ошибка валидации Id
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateProfileWithFailedValidateId(): void
    {
        $formData = new UserProfileUpdateForm($this->createUserWithEmail('update-profile-failed-validate-id@example.com'));
        $formData->id = 0;

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(sprintf("Пользователь с ID '%s' не найден.", $formData->id));

        $this->assertTrue(static::$userUpdateCase->updateProfile($formData));
    }

    /**
     * @return void Редактирование профиля, ошибка валидации Name
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateProfileWithFailedValidateName(): void
    {
        $formData = new UserProfileUpdateForm($this->createUserWithEmail('update-profile-failed-validate-name@example.com'));
        $formData->name = '';

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[name] This value should not be blank.',
            '[name] This value is too short. It should have 1 character or more.',
        ]));

        $this->assertTrue(static::$userUpdateCase->updateProfile($formData));
    }

    /**
     * @return void Редактирование профиля, ошибка валидации E-mail
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateProfileWithFailedValidateEmail(): void
    {
        $formData = new UserProfileUpdateForm($this->createUserWithEmail('update-profile-failed-validate-email@example.com'));
        $formData->email = '';

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(ValidateDtoService::formattedError($formData, [
            '[email] This value should not be blank.',
            '[email] This value is too short. It should have 3 characters or more.',
        ]));

        $this->assertTrue(static::$userUpdateCase->updateProfile($formData));
    }

    /**
     * @return void Редактирование профиля, ошибка валидации E-mail (уже используется)
     * @throws ServiceException В случае ошибки
     */
    public function testUpdateProfileWithFailedValidateEmailAlreadyUsed(): void
    {
        $alreadyCreatedUser = $this->createUserWithEmail('profile-update-already-created-user@example.com');

        $formData = new UserProfileUpdateForm($this->createUserWithEmail('update-profile-failed-validate-email-already-used@example.com'));
        $formData->email = $alreadyCreatedUser->getEmail();

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(sprintf("E-mail адрес '%s' уже используется другим пользователем.", $formData->email));

        $this->assertTrue(static::$userUpdateCase->updateProfile($formData));
    }
}

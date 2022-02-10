<?php
namespace App\Users\DataFixtures\ORM;

use App\Core\DataFixtures\ORM\BaseFixture;
use App\Core\Exception\AppException;
use App\Users\Dto\User\UserCreateForm;
use App\Users\Entity\UserInterface;
use App\Users\UseCase\User\UserCreateCase;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectManager;

/**
 * User Fixtures
 */
final class UserFixtures extends BaseFixture
{
    /**
     * @var UserCreateCase User Create Case
     */
    private UserCreateCase $userCreateCase;

    /**
     * Конструктор класса
     *
     * @param UserCreateCase $userCreateCase
     * @return void
     */
    public function __construct(
        UserCreateCase $userCreateCase
    )
    {
        $this->userCreateCase = $userCreateCase;
    }

    /**
     * @inheritdoc
     * @throws AppException|ORMException
     */
    public function load(ObjectManager $manager)
    {
        parent::load($manager);

        // загрузка User Fixtures
        $this->loadUserFixtures();
    }

    /**
     * Загрузка User Fixtures
     *
     * @throws AppException|ORMException
     */
    private function loadUserFixtures(): void
    {
        $users = [
            [
                'name' => 'Ivan Katkov',
                'email' => 'ivan@webspec.ru',
                'password' => 'ivan@webspec.ru',
                'is_admin' => true,
                'roles' => [
                    UserInterface::ROLE_USER,
                    UserInterface::ROLE_ADMIN,
                ],
            ], [
                'name' => 'Demo User',
                'email' => 'demo@webspec.ru',
                'password' => 'demo@webspec.ru',
                'is_admin' => false,
                'roles' => [
                    UserInterface::ROLE_USER,
                ],
            ],
        ];

        foreach ($users as $userData) {
            $formData = new UserCreateForm();
            $formData->name = $userData['name'];
            $formData->email = $userData['email'];
            $formData->password = $userData['password'];
            $formData->is_admin = $userData['is_admin'];
            $formData->roles = $userData['roles'];

            $this->userCreateCase->create($formData);
        }
    }
}

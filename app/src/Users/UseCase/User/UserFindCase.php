<?php
namespace App\Users\UseCase\User;

use App\Users\Entity\User;
use App\Users\Repository\UserRepository;
use App\Core\Exception\NotFoundEntityException;

/**
 * User Case: Найти пользователя по различным критериям
 */
final class UserFindCase
{
    /**
     * @var UserRepository User Repository
     */
    private UserRepository $userRepository;

    /**
     * Конструктор сервиса
     *
     * @param UserRepository $userRepository User Repository
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $id ID пользователя
     * @param bool $isActive Выборка только активного пользователя
     * @return User Информация о пользователе
     * @throws NotFoundEntityException В случае если пользователь не найден
     */
    public function getUserById(int $id, bool $isActive = true): User
    {
        $user = $this->userRepository->findOneById($id, $isActive);
        if (empty($user)) {
            throw new NotFoundEntityException(sprintf("Пользователь с ID '%s' не найден.", $id));
        }

        return $user;
    }

    /**
     * @param string $email E-mail пользователя
     * @param bool $isActive Выборка только активного пользователя
     * @return User Информация о пользователе
     * @throws NotFoundEntityException В случае если пользователь не найден
     */
    public function getUserByEmail(string $email, bool $isActive = true): User
    {
        $email = trim(mb_strtolower($email));
        $user = $this->userRepository->findOneByEmail($email, $isActive);
        if (empty($user)) {
            throw new NotFoundEntityException(sprintf("Пользователь с E-mail '%s' не найден.", $email));
        }

        return $user;
    }
}

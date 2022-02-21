<?php
namespace App\Users\Repository;

use App\Users\Entity\User;
use App\Users\Entity\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for User Entity
 *
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param int $id ID пользователя
     * @param bool $isActive Активный пользователь?
     * @return User|null Найти пользователя по ID
     */
    public function findOneById(int $id, bool $isActive = true): ?User
    {
        $criteria = $isActive ? ['status' => UserInterface::STATUS_ACTIVE] : [];
        $criteria['id'] = $id;

        return $this->findOneBy($criteria);
    }

    /**
     * @param string $email E-mail пользователя
     * @param bool $isActive Активный пользователь?
     * @return User|null Найти пользователя по E-mail
     */
    public function findOneByEmail(string $email, bool $isActive = true): ?User
    {
        $criteria = $isActive ? ['status' => UserInterface::STATUS_ACTIVE] : [];
        $criteria['email'] = $email;

        return $this->findOneBy($criteria);
    }

    /**
     * @param string $token E-mail Verified Token пользователя
     * @return User|null Найти пользователя по E-mail Verified Token
     */
    public function findOneByEmailVerifiedToken(string $token) : ?User
    {
        return $this->findOneBy([
            'status' => UserInterface::STATUS_ACTIVE,
            'email_verified_token' => $token,
        ]);
    }

    /**
     * @param string $token E-mail Subscribed Token пользователя
     * @return User|null Найти пользователя по E-mail Subscribed Token
     */
    public function findOneByEmailSubscribedToken(string $token) : ?User
    {
        return $this->findOneBy([
            'status' => UserInterface::STATUS_ACTIVE,
            'email_subscribed_token' => $token,
        ]);
    }

    /**
     * @param string $token Password Restore Token пользователя
     * @return User|null Найти пользователя по Password Restore Token
     */
    public function findOneByPasswordRestoreToken(string $token) : ?User
    {
        return $this->findOneBy([
            'status' => UserInterface::STATUS_ACTIVE,
            'password_restore_token' => $token,
        ]);
    }
}

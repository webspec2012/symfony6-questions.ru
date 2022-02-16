<?php
namespace App\Users\UseCase\User;

use App\Core\Exception\ServiceException;
use App\Core\Pagination\Paginator;
use App\Users\Dto\User\UserSearchForm;
use App\Users\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * User Case: Листинг пользователей
 */
final class UserListingCase
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
        UserRepository $userRepository,
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Постраничный листинг пользователей
     *
     * @param UserSearchForm $form Форма поиска
     * @param int $page Номер страницы
     * @param int $pageSize Количество записей на страницу
     * @return Paginator
     * @throws ServiceException
     */
    public function listingWithPaginate(UserSearchForm $form, int $page = 1, int $pageSize = 30): Paginator
    {
        try {
            return (new Paginator($this->buildQuery($form), $pageSize))->paginate($page);
        } catch (\Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     * Листинг пользователей без постраничной навигации
     *
     * @param UserSearchForm $form Форма поиска
     * @return iterable Результат выборки
     * @throws ServiceException
     */
    public function listingWithoutPaginate(UserSearchForm $form): iterable
    {
        try {
            return $this->buildQuery($form)->getQuery()->toIterable();
        } catch (\Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     * Формирование объекта выборки
     *
     * @param UserSearchForm $form Dto с данными для фильтрации
     * @return QueryBuilder Объект выборки с учётом указанных данных для фильтрации
     * @throws ServiceException
     */
    protected function buildQuery(UserSearchForm $form): QueryBuilder
    {
        $query = $this->userRepository->createQueryBuilder('u');

        // filters
        if (!empty($form->id)) {
            $query->andWhere('u.id = :id')
                ->setParameter('id', $form->id);
        }

        if (!empty($form->status)) {
            $query->andWhere('u.status = :status')
                ->setParameter('status', $form->status);
        }

        if (!empty($form->name)) {
            $query->andWhere('u.username like :username')
                ->setParameter('username', '%'.$form->name.'%');
        }

        if (!empty($form->email)) {
            $query->andWhere('u.email like :email')
                ->setParameter('email', '%'.$form->email.'%');
        }

        if (!empty($form->role)) {
            $query->andWhere("JSON_CONTAINS(u.roles, :role) = 1")
                ->setParameter('role', sprintf('"%s"', $form->role));
        }

        // order by
        $availableOrdersBy = [
            'u.id_DESC' => ['u.id' => 'DESC'],
            'u.id_ASC' => ['u.id' => 'ASC'],
        ];

        if (!empty($form->orderBy)) {
            if (!isset($availableOrdersBy[$form->orderBy])) {
                throw new ServiceException(sprintf("Направление сортировки '%s' не поддерживается", $form->orderBy));
            }

            foreach ($availableOrdersBy[$form->orderBy] as $key => $value) {
                $query->addOrderBy($key, $value);
            }
        }

        return $query;
    }
}

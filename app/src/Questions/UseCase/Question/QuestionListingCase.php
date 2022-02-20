<?php
namespace App\Questions\UseCase\Question;

use App\Core\Exception\ServiceException;
use App\Core\Service\Pagination\DbPaginator;
use App\Core\Service\Pagination\PaginatorInterface;
use App\Questions\Dto\Question\QuestionSearchForm;
use App\Questions\Entity\Question\QuestionInterface;
use App\Questions\Repository\QuestionRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Question Case: Листинг вопросов
 */
final class QuestionListingCase
{
    /**
     * @var QuestionRepository Question Repository
     */
    private QuestionRepository $questionRepository;

    /**
     * Конструктор сервиса
     *
     * @param QuestionRepository $questionRepository Question Repository
     *
     * @return void
     */
    public function __construct(
        QuestionRepository $questionRepository,
    )
    {
        $this->questionRepository = $questionRepository;
    }

    /**
     * Постраничный листинг вопросов
     *
     * @param QuestionSearchForm $form Форма поиска
     * @param int $page Номер страницы
     * @param int $pageSize Количество записей на страницу
     * @return PaginatorInterface
     * @throws ServiceException
     */
    public function listingWithPaginate(QuestionSearchForm $form, int $page = 1, int $pageSize = 30): PaginatorInterface
    {
        try {
            return (new DbPaginator($this->buildQuery($form)))->paginate($page, $pageSize);
        } catch (\Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     * Листинг вопросов без постраничной навигации
     *
     * @param QuestionSearchForm $form Форма поиска
     * @return iterable Результат выборки
     * @throws ServiceException
     */
    public function listingWithoutPaginate(QuestionSearchForm $form): iterable
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
     * @param QuestionSearchForm $form Dto с данными для фильтрации
     * @return QueryBuilder Объект выборки с учётом указанных данных для фильтрации
     * @throws ServiceException
     */
    protected function buildQuery(QuestionSearchForm $form): QueryBuilder
    {
        $query = $this->questionRepository
            ->createQueryBuilder('u');

        // joins
        $query->leftJoin('u.created_by', 'cb');
        $query->leftJoin('u.category', 'c');

        // filters
        if (!empty($form->id)) {
            $query->andWhere('u.id = :id')
                ->setParameter('id', $form->id);
        }

        if (!empty($form->status)) {
            $query->andWhere('u.status = :status')
                ->setParameter('status', $form->status);
        } else {
            // пока специально не запросили удалённые, они не будут отображены в выборке
            $query->andWhere('u.status != :status')
                ->setParameter('status', QuestionInterface::STATUS_DELETED);
        }

        if (!empty($form->category)) {
            $query->andWhere('u.category = :category')
                ->setParameter('category', $form->category);
        }

        if (!empty($form->query)) {
            $query->andWhere('u.title like :query OR u.text like :query')
                ->setParameter('query', '%'.$form->query.'%');
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

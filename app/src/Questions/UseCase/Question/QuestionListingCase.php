<?php
namespace App\Questions\UseCase\Question;

use App\Core\Exception\ServiceException;
use App\Core\Pagination\Paginator;
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
     * @return Paginator
     * @throws ServiceException
     */
    public function listingWithPaginate(QuestionSearchForm $form, int $page = 1, int $pageSize = 30): Paginator
    {
        try {
            return (new Paginator($this->buildQuery($form), $pageSize))->paginate($page);
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
        $query = $this->questionRepository->createQueryBuilder('u');

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
            $query->andWhere('u.category_id = :category_id')
                ->setParameter('category_id', $form->category);
        }

        if (!empty($form->title)) {
            $query->andWhere('u.title like :title')
                ->setParameter('title', '%'.$form->title.'%');
        }

        if (!empty($form->text)) {
            $query->andWhere("MATCH (u.text AGAINST :text)")
                ->setParameter('text', $form->text.'*');
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

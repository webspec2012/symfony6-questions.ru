<?php
namespace App\Questions\UseCase\Answer;

use App\Core\Exception\ServiceException;
use App\Core\Pagination\Paginator;
use App\Questions\Dto\Answer\AnswerSearchForm;
use App\Questions\Entity\Answer\AnswerInterface;
use App\Questions\Repository\AnswerRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Answer Case: Листинг ответов
 */
final class AnswerListingCase
{
    /**
     * @var AnswerRepository Answer Repository
     */
    private AnswerRepository $answerRepository;

    /**
     * Конструктор сервиса
     *
     * @param AnswerRepository $answerRepository Answer Repository
     *
     * @return void
     */
    public function __construct(
        AnswerRepository $answerRepository,
    )
    {
        $this->answerRepository = $answerRepository;
    }

    /**
     * Постраничный листинг ответов
     *
     * @param AnswerSearchForm$form Форма поиска
     * @param int $page Номер страницы
     * @param int $pageSize Количество записей на страницу
     * @return Paginator
     * @throws ServiceException
     */
    public function listingWithPaginate(AnswerSearchForm$form, int $page = 1, int $pageSize = 30): Paginator
    {
        try {
            return (new Paginator($this->buildQuery($form), $pageSize))->paginate($page);
        } catch (\Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     * Листинг ответов без постраничной навигации
     *
     * @param AnswerSearchForm$form Форма поиска
     * @return iterable Результат выборки
     * @throws ServiceException
     */
    public function listingWithoutPaginate(AnswerSearchForm$form): iterable
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
     * @param AnswerSearchForm $form Dto с данными для фильтрации
     * @return QueryBuilder Объект выборки с учётом указанных данных для фильтрации
     * @throws ServiceException
     */
    protected function buildQuery(AnswerSearchForm $form): QueryBuilder
    {
        $query = $this->answerRepository->createQueryBuilder('u');

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
                ->setParameter('status', AnswerInterface::STATUS_DELETED);
        }

        if (!empty($form->question)) {
            $query->andWhere('u.question_id = :question_id')
                ->setParameter('question_id_id', $form->question);
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

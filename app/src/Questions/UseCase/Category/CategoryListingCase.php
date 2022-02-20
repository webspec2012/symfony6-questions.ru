<?php
namespace App\Questions\UseCase\Category;

use App\Core\Exception\ServiceException;
use App\Core\Service\Pagination\DbPaginator;
use App\Core\Service\Pagination\PaginatorInterface;
use App\Questions\Dto\Category\CategorySearchForm;
use App\Questions\Entity\Category\CategoryInterface;
use App\Questions\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Category Case: Листинг категорий
 */
final class CategoryListingCase
{
    /**
     * @var CategoryRepository Category Repository
     */
    private CategoryRepository $categoryRepository;

    /**
     * Конструктор сервиса
     *
     * @param CategoryRepository $categoryRepository Category Repository
     *
     * @return void
     */
    public function __construct(
        CategoryRepository $categoryRepository,
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param bool $isPublished Только опубликованных?
     * @return array Список категорий для dropDown списка
     * @throws ServiceException В случае ошибки
     */
    public function getCategoriesForDropdown(bool $isPublished): array
    {
        $items = [];
        $categories = $this->getCategories($isPublished);
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $items[$category->getId()] = $category->getTitle();
            }
        }

        return $items;
    }

    /**
     * @param bool $isPublished Только опубликованных?
     * @return CategoryInterface[] Список категорий
     * @throws ServiceException В случае ошибки
     */
    public function getCategories(bool $isPublished): array
    {
        $formData = new CategorySearchForm();
        $formData->orderBy = 'u.title_ASC';
        if ($isPublished) {
            $formData->status = CategoryInterface::STATUS_PUBLISHED;
        }

        return $this->iterableToArray($this->listingWithoutPaginate($formData));
    }

    /**
     * Постраничный листинг категорий
     *
     * @param CategorySearchForm $form Форма поиска
     * @param int $page Номер страницы
     * @param int $pageSize Количество записей на страницу
     * @return PaginatorInterface
     * @throws ServiceException
     */
    public function listingWithPaginate(CategorySearchForm $form, int $page = 1, int $pageSize = 30): PaginatorInterface
    {
        try {
            return (new DbPaginator($this->buildQuery($form)))->paginate($page, $pageSize);
        } catch (\Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     * Листинг категорий без постраничной навигации
     *
     * @param CategorySearchForm $form Форма поиска
     * @return iterable Результат выборки
     * @throws ServiceException
     */
    public function listingWithoutPaginate(CategorySearchForm $form): iterable
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
     * @param CategorySearchForm $form Dto с данными для фильтрации
     * @return QueryBuilder Объект выборки с учётом указанных данных для фильтрации
     * @throws ServiceException
     */
    protected function buildQuery(CategorySearchForm $form): QueryBuilder
    {
        $query = $this->categoryRepository->createQueryBuilder('u');

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
                ->setParameter('status', CategoryInterface::STATUS_DELETED);
        }

        if (!empty($form->title)) {
            $query->andWhere('u.title like :title')
                ->setParameter('title', '%'.$form->title.'%');
        }

        if (!empty($form->slug)) {
            $query->andWhere('u.slug like :slug')
                ->setParameter('slug', '%'.$form->slug.'%');
        }

        // order by
        $availableOrdersBy = [
            'u.id_DESC' => ['u.id' => 'DESC'],
            'u.id_ASC' => ['u.id' => 'ASC'],
            'u.title_DESC' => ['u.title' => 'DESC'],
            'u.title_ASC' => ['u.title' => 'ASC'],
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

    /**
     * Iterable to Array
     *
     * @param iterable $iterable
     * @return array
     */
    private function iterableToArray(iterable $iterable): array
    {
        return $iterable instanceof \Traversable ? iterator_to_array($iterable) : $iterable;
    }
}

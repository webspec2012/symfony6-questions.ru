<?php
namespace App\Core\Service\Pagination;

use Iterator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * Paginator для реализации через базы данных с использованием Doctrine
 */
final class DbPaginator implements PaginatorInterface
{
    /**
     * @var QueryBuilder Query Builder
     */
    private QueryBuilder $queryBuilder;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var int Текущая страница
     */
    private int $currentPage = 1;

    /**
     * @var int Размер страницы
     */
    private int $pageSize = 30;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var Iterator Результаты выборки
     */
    private Iterator $results;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var int Общее количество выборки
     */
    private int $numResults;

    /**
     * Конструктор
     *
     * @param QueryBuilder $queryBuilder Query Builder
     *
     * @return void
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Сформировать пагинацию
     *
     * @param int $page Текущая страница
     * @param int $pageSize Размер страницы
     *
     * @return PaginatorInterface
     * @throws \Exception
     */
    public function paginate(int $page = 1, int $pageSize = 30): PaginatorInterface
    {
        $this->currentPage = max(1, $page);
        $this->pageSize = max(1, $pageSize);

        $query = $this->queryBuilder
            ->setFirstResult(($this->currentPage - 1) * $this->pageSize)
            ->setMaxResults($this->pageSize)
            ->getQuery();

        $paginator = new DoctrinePaginator($query, true);
        $this->results = $paginator->getIterator();
        $this->numResults = $paginator->count();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @inheritdoc
     */
    public function getLastPage(): int
    {
        return (int) ceil($this->numResults / $this->pageSize);
    }

    /**
     * @inheritdoc
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @inheritdoc
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * @inheritdoc
     */
    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    /**
     * @inheritdoc
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    /**
     * @inheritdoc
     */
    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    /**
     * @inheritdoc
     */
    public function hasToPaginate(): bool
    {
        return $this->numResults > $this->pageSize;
    }

    /**
     * @inheritdoc
     */
    public function getNumResults(): int
    {
        return $this->numResults;
    }

    /**
     * @inheritdoc
     */
    public function getResults(): Iterator
    {
        return $this->results;
    }
}

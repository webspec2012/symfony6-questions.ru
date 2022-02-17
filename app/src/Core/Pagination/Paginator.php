<?php
namespace App\Core\Pagination;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * Разбивает Query Builder на постраничный вывод
 */
final class Paginator
{
    /**
     * @var QueryBuilder Query Builder
     */
    private QueryBuilder $queryBuilder;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var int Текущая страница
     */
    private int $currentPage;

    /**
     * @var int Размер страницы
     */
    private int $pageSize;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var iterable Результаты выборки
     */
    private iterable $results;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var int Общее количество выборки
     */
    private int $numResults;

    /**
     * Конструктор
     *
     * @param QueryBuilder $queryBuilder Query Builder
     * @param int $pageSize Размер страницы
     * @return void
     */
    public function __construct(QueryBuilder $queryBuilder, int $pageSize)
    {
        $this->queryBuilder = $queryBuilder;
        $this->pageSize = $pageSize;
    }

    /**
     * Сформировать пагинацию
     *
     * @param int $page Текущая страница
     * @return Paginator
     */
    public function paginate(int $page = 1): Paginator
    {
        $this->currentPage = max(1, $page);

        $query = $this->queryBuilder
            ->setFirstResult(($this->currentPage - 1) * $this->pageSize)
            ->setMaxResults($this->pageSize)
            ->getQuery();

        if (0 === \count($this->queryBuilder->getDQLPart('join'))) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        $paginator = new DoctrinePaginator($query, true);

        $useOutputWalkers = \count($this->queryBuilder->getDQLPart('having') ?: []) > 0;
        $paginator->setUseOutputWalkers($useOutputWalkers);

        $this->results = $paginator->getIterator();
        $this->numResults = $paginator->count();

        return $this;
    }

    /**
     * @return int Текущая страница
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int Последняя страница
     */
    public function getLastPage(): int
    {
        return (int) ceil($this->numResults / $this->pageSize);
    }

    /**
     * @return int Размер страницы
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @return bool Имеет предыдущую страницу?
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * @return int Предыдущая страница
     */
    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    /**
     * @return bool Имеет следующую страницу?
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    /**
     * @return int Следующая страница
     */
    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    /**
     * @return bool Имеет постраничный вывод?
     */
    public function hasToPaginate(): bool
    {
        return $this->numResults > $this->pageSize;
    }

    /**
     * @return int Общее количество выборки
     */
    public function getNumResults(): int
    {
        return $this->numResults;
    }

    /**
     * @return iterable Результаты выборки
     */
    public function getResults(): iterable
    {
        return $this->results;
    }
}

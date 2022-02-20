<?php
namespace App\Core\Service\Pagination;

use Iterator;

/**
 * Интерфейс для Paginator
 */
interface PaginatorInterface
{
    /**
     * Сформировать пагинацию
     *
     * @param int $page Текущая страница
     * @param int $pageSize Размер страницы
     *
     * @return PaginatorInterface
     */
    public function paginate(int $page = 1, int $pageSize = 30): PaginatorInterface;

    /**
     * @return int Текущая страница
     */
    public function getCurrentPage(): int;

    /**
     * @return int Последняя страница
     */
    public function getLastPage(): int;

    /**
     * @return int Размер страницы
     */
    public function getPageSize(): int;

    /**
     * @return bool Имеет предыдущую страницу?
     */
    public function hasPreviousPage(): bool;

    /**
     * @return int Предыдущая страница
     */
    public function getPreviousPage(): int;

    /**
     * @return bool Имеет следующую страницу?
     */
    public function hasNextPage(): bool;

    /**
     * @return int Следующая страница
     */
    public function getNextPage(): int;

    /**
     * @return bool Имеет постраничный вывод?
     */
    public function hasToPaginate(): bool;

    /**
     * @return int Общее количество выборки
     */
    public function getNumResults(): int;

    /**
     * @return Iterator Результаты выборки
     */
    public function getResults(): Iterator;
}
